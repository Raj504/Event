<?php

namespace App\Http\Controllers\FrontEnd\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Event\BookingController;
use App\Models\BasicSettings\Basic;
use App\Models\Earning;
use App\Models\PaymentGateway\OnlineGateway;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;




class RazorpayController extends Controller
{
  private $key, $secret, $api;

  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('razorpay')->first();
    $razorpayData = json_decode($data->information, true);

    $this->key = $razorpayData['key'];
    $this->secret = $razorpayData['secret'];

    $this->api = new Api($this->key, $this->secret);
  }

  public function bookingProcess(Request $request, $event_id)
{
    Log::info('Starting booking process', ['event_id' => $event_id]);

    $rules = [
        'fname' => 'required',
        'lname' => 'required',
        'email' => 'required',
        'phone' => 'required',
        'country' => 'required',
        'address' => 'required',
        'gateway' => 'required',
    ];

    $message = [
        'fname.required' => 'The first name field is required',
        'lname.required' => 'The last name field is required',
        'gateway.required' => 'The payment gateway field is required',
    ];

    Log::info('Validating request data');
    $request->validate($rules, $message);

    $currencyInfo = $this->getCurrencyInfo();
    Log::info('Currency information retrieved', ['currencyInfo' => $currencyInfo]);

    $total = Session::get('grand_total');
    $quantity = Session::get('quantity');
    $discount = Session::get('discount');
    $tax_amount = Session::get('tax');
    $total_early_bird_dicount = Session::get('total_early_bird_dicount');
    Log::info('Session data retrieved', compact('total', 'quantity', 'discount', 'tax_amount', 'total_early_bird_dicount'));

    $basicSetting = Basic::select('commission')->first();
    Log::info('Basic setting retrieved', ['commission' => $basicSetting->commission]);

    $commission_amount = ($total * $basicSetting->commission) / 100;
    Log::info('Commission calculated', ['commission_amount' => $commission_amount]);

    if ($currencyInfo->base_currency_text !== 'INR') {
      Log::info('Converting USD to INR', ['original_amount' => $total]);
  
      try {
          $convertedAmount = $this->convertToINR($total, $currencyInfo->base_currency_text, 'INR');
          Log::info('Conversion successful', ['converted_amount' => $convertedAmount]);
  
          $total = $convertedAmount; 
      } catch (Exception $e) {
          Log::error('Currency conversion failed', ['error' => $e->getMessage()]);
          return redirect()->back()->with('currency_error', 'Unable to process currency conversion.')->withInput();
      }
  }
    $arrData = [
        'event_id' => $event_id,
        'price' => $total,
        'tax' => $tax_amount,
        'commission' => $commission_amount,
        'quantity' => $quantity,
        'discount' => $discount,
        'total_early_bird_dicount' => $total_early_bird_dicount,
        'currencyText' => $currencyInfo->base_currency_text,
        'currencyTextPosition' => $currencyInfo->base_currency_text_position,
        'currencySymbol' => $currencyInfo->base_currency_symbol,
        'currencySymbolPosition' => $currencyInfo->base_currency_symbol_position,
        'fname' => $request->fname,
        'lname' => $request->lname,
        'email' => $request->email,
        'phone' => $request->phone,
        'country' => $request->country,
        'state' => $request->state,
        'city' => $request->city,
        'zip_code' => $request->city,
        'address' => $request->address,
        'paymentMethod' => 'Razorpay',
        'gatewayType' => 'online',
        'paymentStatus' => 'completed',
    ];
    Log::info('Booking data prepared', $arrData);

    $notifyURL = route('event_booking.razorpay.notify');
    Log::info('Notify URL generated', ['notifyURL' => $notifyURL]);

    $orderData = [
        'receipt' => 'Course Enrolment',
        'amount' => (($total + $tax_amount) * 100),
        'currency' => 'INR',
        'payment_capture' => 1, // auto capture
    ];
    Log::info('Order data prepared', $orderData);

    try {
        $razorpayOrder = $this->api->order->create($orderData);
        Log::info('Razorpay order created successfully', ['order_id' => $razorpayOrder->id]);
    } catch (Exception $e) {
        Log::error('Error creating Razorpay order', ['exception' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Something went wrong or invalid API key!')->withInput();
    }

    $webInfo = DB::table('basic_settings')->select('website_title')->first();
    $buyerName = $request->fname . ' ' . $request->lname;
    $buyerEmail = $request->email;
    $buyerContact = $request->phone;

    $checkoutData = [
        'key' => $this->key,
        'amount' => $orderData['amount'],
        'name' => $webInfo->website_title,
        'description' => 'Event Booking Via Razorpay',
        'prefill' => [
            'name' => $buyerName,
            'email' => $buyerEmail,
            'contact' => $buyerContact,
        ],
        'order_id' => $razorpayOrder->id,
    ];
    Log::info('Checkout data prepared', $checkoutData);

    $jsonData = json_encode($checkoutData);
    Log::info('Checkout data JSON encoded', ['jsonData' => $jsonData]);

    $request->session()->put('event_id', $event_id);
    $request->session()->put('arrData', $arrData);
    $request->session()->put('razorpayOrderId', $razorpayOrder->id);
    Log::info('Session data updated for payment');

    return view('frontend.payment.razorpay', compact('jsonData', 'notifyURL'));
}

  public function notify(Request $request)
  {
    // get the information from session
    $eventId = $request->session()->get('event_id');
    $arrData = $request->session()->get('arrData');
    $razorpayOrderId = $request->session()->get('razorpayOrderId');

    $urlInfo = $request->all();

    // assume that the transaction was successful
    $success = true;

    /**
     * either razorpay_order_id or razorpay_subscription_id must be present.
     * the keys of $attributes array must be follow razorpay convention.
     */
    try {
      $attributes = [
        'razorpay_order_id' => $razorpayOrderId,
        'razorpay_payment_id' => $urlInfo['razorpayPaymentId'],
        'razorpay_signature' => $urlInfo['razorpaySignature']
      ];

      $this->api->utility->verifyPaymentSignature($attributes);
    } catch (SignatureVerificationError $e) {
      $success = false;
    }

    if ($success === true) {
      $enrol = new BookingController();

      $bookingInfo['transcation_type'] = 1;

      // store the course enrolment information in database
      $bookingInfo = $enrol->storeData($arrData);
      // generate an invoice in pdf format
      $invoice = $enrol->generateInvoice($bookingInfo, $eventId);
      //unlink qr code
      @unlink(public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '.svg');
      //end unlink qr code

      // then, update the invoice field info in database
      $bookingInfo->update(['invoice' => $invoice]);

      //add blance to admin revinue
      $earning = Earning::first();
      $earning->total_revenue = $earning->total_revenue + $arrData['price'] + $bookingInfo->tax;
      if ($bookingInfo['organizer_id'] != null) {
        $earning->total_earning = $earning->total_earning + ($bookingInfo->tax + $bookingInfo->commission);
      } else {
        $earning->total_earning = $earning->total_earning + $arrData['price'] + $bookingInfo->tax;
      }
      $earning->save();

      //storeTransaction
      $bookingInfo['paymentStatus'] = 1;
      $bookingInfo['transcation_type'] = 1;

      storeTranscation($bookingInfo);

      //store amount to organizer
      $organizerData['organizer_id'] = $bookingInfo['organizer_id'];
      $organizerData['price'] = $arrData['price'];
      $organizerData['tax'] = $bookingInfo->tax;
      $organizerData['commission'] = $bookingInfo->commission;
      storeOrganizer($organizerData);

      // send a mail to the customer with the invoice
      $enrol->sendMail($bookingInfo);

      // remove all session data
      $request->session()->forget('event_id');
      $request->session()->forget('selTickets');
      $request->session()->forget('arrData');
      $request->session()->forget('paymentId');
      $request->session()->forget('discount');
      $request->session()->forget('razorpayOrderId');
      return redirect()->route('event_booking.complete', ['id' => $eventId, 'booking_id' => $bookingInfo->id]);
    } else {
      // remove all session data
      $request->session()->forget('event_id');
      $request->session()->forget('arrData');
      $request->session()->forget('razorpayOrderId');
      $request->session()->forget('discount');

      return redirect()->route('event_booking.cancel', ['id' => $eventId]);
    }
  }

  private function convertToINR($amount, $fromCurrency = 'USD', $toCurrency = 'INR')
{
    $apiKey = '058bbebccff9dd20609cd10c'; 
    $url = "https://api.exchangerate-api.com/v4/latest/{$fromCurrency}";

    try {
        $response = Http::get($url);
        $exchangeRates = $response->json();

        if (isset($exchangeRates['rates'][$toCurrency])) {
            $conversionRate = $exchangeRates['rates'][$toCurrency];
            return $amount * $conversionRate;
        } else {
            throw new Exception("Conversion rate not found for {$toCurrency}");
        }
    } catch (Exception $e) {
        Log::error('Currency conversion error', ['error' => $e->getMessage()]);
        throw $e; 
    }
}
}
