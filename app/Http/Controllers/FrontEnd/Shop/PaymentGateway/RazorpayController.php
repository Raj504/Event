<?php

namespace App\Http\Controllers\FrontEnd\Shop\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Shop\OrderController;
use App\Models\BasicSettings\Basic;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\ShopManagement\ShippingCharge;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayController extends Controller
{
  private $key, $secret, $api;

  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('razorpay')->first();

    if (!$data) {
      \Log::error('Razorpay Configuration Error: No configuration found in OnlineGateway table.');
      throw new \Exception('No configuration found for Razorpay.');
  }
    $razorpayData = json_decode($data->information, true);

    if (!isset($razorpayData['key']) || !isset($razorpayData['secret'])) {
      \Log::error('Razorpay Configuration Error: Missing key or secret in the configuration.');
      throw new \Exception('Invalid Razorpay configuration.');
  }

    $this->key = $razorpayData['key'];
    $this->secret = $razorpayData['secret'];

    try {
      $this->api = new Api($this->key, $this->secret);
  } catch (\JsonException $e) {
    \Log::error('JSON Decode Error: ' . $e->getMessage());
    throw new \Exception('Invalid JSON format for Razorpay configuration.');
  } catch (\Exception $e) {
    \Log::error('Razorpay Initialization Error: ' . $e->getMessage());
    throw $e;
}

}

  public function enrolmentProcess(Request $request)
  {
      try {
    $enrol = new OrderController();


    $currencyInfo = $this->getCurrencyInfo();
    $cart_items = Session::get('cart');

    if (!$cart_items || !is_array($cart_items)) {
      \Log::error('Cart Error: Cart is empty or invalid format.');
      return redirect()->back()->with('error', 'Your cart is empty or invalid.')->withInput();
  }

    $total = 0;
    $quantity = 0;
    foreach ($cart_items as $p) {

      if (!isset($p['price']) || !isset($p['qty'])) {
        \Log::error('Cart Item Error: Missing price or quantity for item: ' . json_encode($p));
        continue; // Skip invalid cart items
    }

      $total += $p['price'] * $p['qty'];
      $quantity += $p['price'] * $p['qty'];
    }
    if ($request->shipping_method) {
      $shipping_cost = ShippingCharge::where('id', $request->shipping_method)->first();
      $shipping_charge = $shipping_cost->charge;
      $shipping_method = $shipping_cost->title;
    } else {
      $shipping_charge = 0;
      $shipping_method = NULL;
    }

    $discount = Session::get('Shop_discount');
    $tax = Basic::select('shop_tax')->first();
    $tax_percentage = $tax->shop_tax;
    $total_tax_amount = ($tax_percentage / 100) * ($total - $discount);
    $grand_total = ($shipping_charge + $total + $total_tax_amount) - $discount;
    // checking whether the currency is set to 'INR' or not
    if ($currencyInfo->base_currency_text !== 'INR') {
      \Log::error('Currency Error: Invalid currency for Razorpay payment. Currency: ' . $currencyInfo->base_currency_text);

      return redirect()->back()->with('error', 'Invalid currency for razorpay payment.')->withInput();
    }

    if (Auth::guard('customer')->user()) {
      $user_id = Auth::guard('customer')->user()->id;
    } else {
      $user_id = 0;
    }
    $arrData = array(
      'user_id' => $user_id,
      'fname' => $request->fname,
      'lname' => $request->lname,
      'email' => $request->email,
      'phone' => $request->phone,
      'country' => $request->country,
      'state' => $request->state,
      'city' => $request->city,
      'zip_code' => $request->zip_code,
      'address' => $request->address,

      's_fname' => $request->sameas_shipping == NULL ? $request->s_fname : $request->fname,
      's_lname' => $request->sameas_shipping == NULL ? $request->s_lname : $request->lname,
      's_email' => $request->sameas_shipping == NULL ? $request->s_email : $request->email,
      's_phone' => $request->sameas_shipping == NULL ? $request->s_phone : $request->phone,
      's_country' => $request->sameas_shipping == NULL ? $request->s_country : $request->country,
      's_state' => $request->sameas_shipping == NULL ? $request->s_state : $request->state,
      's_city' => $request->sameas_shipping == NULL ? $request->s_city : $request->city,
      's_zip_code' => $request->sameas_shipping == NULL ? $request->s_city : $request->city,
      's_address' => $request->sameas_shipping == NULL ? $request->s_address : $request->address,

      'cart_total' => $total,
      'discount' => $discount,
      'tax_percentage' => $tax_percentage,
      'tax' => $total_tax_amount,
      'grand_total' => $grand_total,
      'currency_code' => '',

      'shipping_charge' => $shipping_charge,
      'shipping_method' => $shipping_method,
      'order_number' => uniqid(),
      'charge_id' => $request->shipping_method,

      'method' => 'Razorpay',
      'gateway_type' => 'online',
      'payment_status' => 'completed',
      'order_status' => 'pending',
      'tnxid' => '',
    );

    $notifyURL = route('product_order.razorpay.notify');

    // create order data

    try {
      // Log Razorpay API keys
      \Log::info('Using Razorpay Key: ' . $this->key);
      \Log::info('Creating Razorpay Order with secret: ' . $this->secret);
    $orderData = [
      'receipt'         => 'Product Order',
      'amount'          => $grand_total * 100,
      'currency'        => 'INR',
      'payment_capture' => 1 // auto capture
    ];
    \Log::info('Razorpay Order Data: ' . json_encode($orderData));

    
      $razorpayOrder = $this->api->order->create($orderData);
      \Log::info('Razorpay Order Created Successfully: ' . json_encode($razorpayOrder));
    } catch (Exception $e) {
      // Enhanced error logging
      \Log::error('Razorpay Order Creation Error: ' . $e->getMessage());
      \Log::error('Razorpay API Key Used: ' . $this->key);
      \Log::error('Razorpay API Secret Used: ' . $this->secret);

      return redirect()->back()->with('error', 'Razorpay Order creation failed. Please check API credentials or network connection.');

    }


    $webInfo = DB::table('basic_settings')->select('website_title')->first();
    $buyerName = $request->fname . ' ' . $request->lname;
    $buyerEmail = $request->email;
    $buyerContact = $request->phone;

    // create checkout data
    $checkoutData = [
      'key'               => $this->key,
      'amount'            => $orderData['amount'],
      'name'              => $webInfo->website_title,
      'description'       => 'Product Order Payment Via Razorpay',
      'prefill'           => [
        'name'              => $buyerName,
        'email'             => $buyerEmail,
        'contact'           => $buyerContact
      ],
      'order_id'          => $razorpayOrder->id
    ];

    $jsonData = json_encode($checkoutData);

    // put some data in session before redirect to razorpay url
    $request->session()->put('arrData', $arrData);
    $request->session()->put('razorpayOrderId', $razorpayOrder->id);

    return view('frontend.payment.razorpay', compact('jsonData', 'notifyURL'));
  } catch (\Exception $e) {
    \Log::error('Enrolment Process Error: ' . $e->getMessage());
    return redirect()->back()->with('error', 'An unexpected error occurred. Please try again.')->withInput();
}
  }

  public function notify(Request $request)
  {
    // get the information from session
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
      $enrol = new OrderController();

      // store the course enrolment information in database
      $orderInfo = $enrol->storeData($arrData);

      //store data to oder items table
      $orderItems = $enrol->storeOders($orderInfo);

      // generate an invoice in pdf format
      $invoice = $enrol->generateInvoice($orderInfo);

      // then, update the invoice field info in database
      $orderInfo->update(['invoice_number' => $invoice]);

      // send a mail to the customer with the invoice
      $enrol->sendMail($orderInfo);

      // remove all session data
      $request->session()->forget('arrData');
      $request->session()->forget('razorpayOrderId');

      return redirect()->route('product_order.complete');
    } else {
      // remove all session data
      $request->session()->forget('arrData');
      $request->session()->forget('razorpayOrderId');

      return redirect()->route('shop.checkout');
    }
  }
}
