<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\CustomPage\Page;
use App\Models\CustomPage\PageContent;
use Illuminate\Http\Request;
use App\Models\Installment;
use App\Models\PaymentGateway\OfflineGateway;
use App\Models\PaymentGateway\OnlineGateway;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\BasicSettings\Basic;
use App\Models\Event\Booking;
use App\Models\Event;

class PageController extends Controller
{
  public function page($slug)
  {
    $language = $this->getLanguage();

    $queryResult['bgImg'] = $this->getBreadcrumb();

    $pageId = PageContent::where('slug', $slug)->firstOrFail()->page_id;

    $queryResult['pageInfo'] = Page::join('page_contents', 'pages.id', '=', 'page_contents.page_id')
      ->where('pages.status', '=', 1)
      ->where('page_contents.language_id', '=', $language->id)
      ->where('page_contents.page_id', '=', $pageId)
      ->firstOrFail();

    return view('frontend.custom-page', $queryResult);
  }


 public function paymentByClient($paymentId)
{
    Session::put('online_gateways', OnlineGateway::where('status', 1)->get());
    Session::put('offline_gateways', OfflineGateway::where('status', 1)->orderBy('serial_number', 'asc')->get());

    $information['online_gateways'] = Session::get('online_gateways');
    $information['offline_gateways'] = Session::get('offline_gateways');
    $information['selTickets'] = Session::get('selTickets');
    $information['total'] = Session::get('total');
    $information['quantity'] = Session::get('quantity');
    $information['total_early_bird_dicount'] = Session::get('total_early_bird_dicount');
    $information['event'] = Booking::where('uuid', $paymentId)->first();
    $information['basicData'] = Basic::select('tax')->first();






    $stripe = OnlineGateway::where('keyword', 'stripe')->first();

    if ($stripe) {
        $stripe_info = json_decode($stripe->information, true);

        if ($stripe_info && isset($stripe_info['key'])) {
            $information['stripe_key'] = $stripe_info['key'];
        } else {
            $information['stripe_key'] = null;
        }
    } else {
        $information['stripe_key'] = null; 
    }
    Auth::check();
    $booking = Booking::where('uuid', $paymentId)->first();
// dd($booking);
    // $information['booking'] = $information['event']; 
    $emiAmount = $booking->emi_amount ?? null;

    return view('payments.installment', $information, compact('emiAmount', 'booking'));
}

public function processInstallmentPayment(Request $request, $bookingId)
{
    $booking = Booking::findOrFail($bookingId);

    // Check if the total paid equals the total EMI amount
    if ($booking->total_paid >= $booking->emi_amount) {
        return back()->withErrors(['error' => 'You have already paid the full amount for this booking.']);
    }

    // Calculate the remaining amount
    $remainingAmount = $booking->emi_amount - $booking->total_paid;

    // Ensure the payment doesn't exceed the remaining amount
    $paymentAmount = $request->input('payment_amount'); // Validate this field in your form
    if ($paymentAmount > $remainingAmount) {
        return back()->withErrors(['error' => 'Payment amount exceeds the remaining balance.']);
    }

    // Process the payment (e.g., integrate Stripe or other gateway here)
    // For simplicity, we'll assume the payment is successful.

    // Update the total_paid
    $booking->total_paid += $paymentAmount;

    // Check if fully paid
    if ($booking->total_paid >= $booking->emi_amount) {
        $booking->payment_status = 'success';
    }

    $booking->save();

    return redirect()->back()->with('success', 'Installment payment successful!');
}


}
