<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Event;
use App\Models\Event\Coupon;
use App\Models\Event\EventCategory;
use App\Models\Event\EventContent;
use App\Models\Event\EventDates;
use App\Models\Event\EventImage;
use App\Models\Event\Ticket;
use App\Models\Event\Wishlist;
use App\Models\Organizer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
  private $now_date_time;
  public function __construct()
  {
    $this->now_date_time = Carbon::now();
  }
  //index
  public function index(Request $request)
  {

    // $event = Event::with('content')->find($eventIds);

    $language = $this->getLanguage();
    $information  = [];
    $categories = EventCategory::where([['language_id', $language->id], ['status', 1]])->orderBy('serial_number', 'asc')->get();
    $information['categories'] = $categories;
    $countries = Country::get();
    $information['countries'] = $countries;


    //for filter
    $category = $location =  $event_type = $min = $max = $keyword = $date1 = $date2 = null;


    if ($request->filled('dates')) {
      $dates = $request->input('dates');
      $dateArray = explode(' ', $dates);

      // Parse and format dates
      $checkinDate = isset($dateArray[0]) ? \Carbon\Carbon::createFromFormat('d-m-Y', trim($dateArray[0]))->startOfDay() : null;
      $checkoutDate = isset($dateArray[1]) ? \Carbon\Carbon::createFromFormat('d-m-Y', trim($dateArray[1]))->endOfDay() : null;

      // Store in session
      session(['checkin_date' => $checkinDate, 'checkout_date' => $checkoutDate]);
  } else {
      // Retrieve dates from session if not in request
      $checkinDate = session('checkin_date');
      $checkoutDate = session('checkout_date');
  }




    if ($request->filled('category')) {
      $category = $request['category'];
      $category = EventCategory::where([['slug', $category], ['status', 1]])->first();
      $category = $category->id;
    }
    $eventSIds = [];
    if ($request->filled('location')) {
      $location = $request['location'];

      $event_contents = EventContent::where(function ($query) use ($location) {
        return $query->where('address', 'like', '%' . $location . '%')
          ->orWhere('city', 'like', '%' . $location . '%')
          ->orWhere('country', 'like', '%' . $location . '%')
          ->orWhere('state', 'like', '%' . $location . '%');
      })->where('language_id', $language->id)->get();

      foreach ($event_contents as $event_content) {
        if (!in_array($event_content->event_id, $eventSIds)) {
          array_push($eventSIds, $event_content->event_id);
        }
      }
    }

    if ($request->filled('event')) {
      $event_type = $request['event'];
    }
    $eventIds = [];

    if ($request->filled('min') && $request->filled('max')) {
      $min = $request['min'];
      $max = $request['max'];

      $tickets = Ticket::where('tickets.f_price', '>=', $min)->where('tickets.f_price', '<=', $max)->get();

      foreach ($tickets as $ticket) {
        if (!in_array($ticket->event_id, $eventIds)) {
          array_push($eventIds, $ticket->event_id);
        }
      }
    }

    if ($request->filled('search-input')) {
      $keyword = $request['search-input'];
    }
    $eventIds2 = [];
    if ($checkinDate && $checkoutDate) {
      try {
          $q_events = EventDates::whereDate('start_date', '<=', $checkinDate)
              ->whereDate('end_date', '>=', $checkoutDate)
              ->pluck('event_id')
              ->toArray();

          $events = Event::whereDate('start_date', '<=', $checkinDate)
              ->whereDate('end_date', '>=', $checkoutDate)
              ->pluck('id')
              ->toArray();

          $eventIds2 = array_unique(array_merge($eventIds2, $q_events, $events));
      } catch (\Exception $e) {
          Log::error('Date Parsing Error:', ['error' => $e->getMessage()]);
      }
  }
        
      
    

    //   $events = Event::whereDate('start_date', '<=', $date1)->whereDate('end_date', '>=', $date1)->get();

    //   foreach ($events as $event) {
    //     if (!in_array($event->id, $eventIds2)) {
    //       array_push($eventIds2, $event->id);
    //     }
    //   }
    // }


    $events = EventContent::join('events', 'events.id', '=', 'event_contents.event_id')
    ->where('event_contents.language_id', $language->id)
    ->when($category, function ($query, $category) {
        return $query->where('event_contents.event_category_id', '=', $category);
    })
    ->when($event_type, function ($query, $event_type) {
        return $query->where('events.event_type', '=', $event_type);
    })
    ->when(($min && $max), function ($query) use ($eventIds) {
        return $query->whereIn('events.id', $eventIds);
    })
    ->when($location, function ($query) use ($eventSIds) {
        return $query->whereIn('events.id', $eventSIds);
    })
    ->when(($eventIds2), function ($query) use ($eventIds2) {
        return $query->whereIn('events.id', $eventIds2);
    })
    ->when($keyword, function ($query, $keyword) {
        return $query->where(function ($q) use ($keyword) {
            $q->where('event_contents.title', 'like', '%' . $keyword . '%')
                ->orWhere('event_contents.address', 'like', '%' . $keyword . '%')
                ->orWhere('event_contents.city', 'like', '%' . $keyword . '%')
                ->orWhere('event_contents.state', 'like', '%' . $keyword . '%')
                ->orWhere('event_contents.country', 'like', '%' . $keyword . '%');
        });
    })
    ->where('events.status', 1)
    ->whereDate('events.end_date_time', '>=', $this->now_date_time)
    ->select('events.*', 'event_contents.title', 'event_contents.description', 'event_contents.city', 'event_contents.state', 'event_contents.country', 'event_contents.address', 'event_contents.zip_code', 'event_contents.slug', 'event_contents.book_date')  // Include book_date here
    ->orderBy('events.id', 'desc')
    ->paginate(9);

$max = Ticket::max('f_price');
$min = Ticket::min('f_price');
$information['max'] = $max;
$information['min'] = $min;
$information['events'] = $events;

$information['checkin_date'] = $checkinDate ? $checkinDate->format('F j, Y') : null;
$information['checkout_date'] = $checkoutDate ? $checkoutDate->format('F j, Y') : null;

return view('frontend.event.event', compact('information', 'events'));
  }

  //details
  public function details($slug, $id)
{
    $language = $this->getLanguage();
    $information = [];


    $checkinDate = session('checkin_date');
    $checkoutDate = session('checkout_date');



    $information['checkin_date'] = $checkinDate ? \Carbon\Carbon::parse($checkinDate)->format('F j, Y') : null;
    $information['checkout_date'] = $checkoutDate ? \Carbon\Carbon::parse($checkoutDate)->format('F j, Y') : null;

    // Remove all session data
    Session::forget('selTickets');
    Session::forget('total');
    Session::forget('quantity');
    Session::forget('total_early_bird_dicount');
    Session::forget('event');
    Session::forget('online_gateways');
    Session::forget('offline_gateways');

    $tickets_count = Ticket::where('event_id', $id)->count();
    $information['tickets_count'] = $tickets_count;

    if ($tickets_count < 1) {
        $content = EventContent::join('events', 'events.id', 'event_contents.event_id')
            ->join('event_images', 'event_images.event_id', '=', 'events.id')
            ->join('event_categories', 'event_categories.id', '=', 'event_contents.event_category_id')
            ->where('event_contents.language_id', $language->id)
            ->where('events.id', $id)
            ->select(
                'events.*', 'event_contents.title', 'event_contents.slug as eventSlug', 'event_contents.description',
                'meta_keywords', 'meta_description', 'event_contents.event_category_id', 'event_categories.name',
                'event_categories.slug', 'event_contents.city', 'event_contents.state', 'event_contents.country',
                'event_contents.address', 'event_contents.zip_code', 'event_contents.refund_policy', 'event_contents.features', 'event_contents.book_date'
              
            )
            ->first();
        if (is_null($content)) {
            Session::flash('alert-type', 'warning');
            Session::flash('message', 'No event content found for ' . $language->name . ' Language');
            return redirect()->route('index');
        }
    } else {
        $content = EventContent::join('events', 'events.id', 'event_contents.event_id')
            ->join('tickets', 'tickets.event_id', '=', 'events.id')
            ->join('event_images', 'event_images.event_id', '=', 'events.id')
            ->join('event_categories', 'event_categories.id', '=', 'event_contents.event_category_id')
            ->where('event_contents.language_id', $language->id)
            ->where('events.id', $id)
            ->select(
                'events.*', 'event_contents.title', 'event_contents.slug as eventSlug', 'event_contents.description',
                'meta_keywords', 'meta_description', 'event_contents.event_category_id', 'event_categories.name',
                'event_categories.slug', 'tickets.price', 'tickets.variations', 'tickets.pricing_type',
                'event_contents.city', 'event_contents.state', 'event_contents.country', 'event_contents.address',
                'event_contents.zip_code', 'event_contents.refund_policy', 'event_contents.features', 'event_contents.book_date'
              
            )
            ->first();
        if (is_null($content)) {
            Session::flash('alert-type', 'warning');
            Session::flash('message', 'No event content found for ' . $language->name . ' Language');
            return redirect()->route('index');
        }
    }

    $information['content'] = $content;
    $images = EventImage::where('event_id', $id)->get();
    $information['images'] = $images;

    $information['organizer'] = '';
    if ($content && $content->organizer_id != NULL) {
        $organizer = Organizer::where('id', $content->organizer_id)->first();
        $information['organizer'] = $organizer;
    }

    $related_events = EventContent::join('events', 'events.id', 'event_contents.event_id')
        ->where('event_contents.language_id', $language->id)
        ->where('event_contents.event_category_id', $content->event_category_id)
        ->where('events.id', '!=', $content->id)
        ->whereDate('events.end_date_time', '>=', $this->now_date_time)
        ->select(
            'events.*', 'event_contents.title', 'event_contents.description', 'event_contents.slug',
            'event_contents.city', 'event_contents.country'
        )
        ->orderBy('events.id', 'desc')
        ->get();

        

    $information['related_events'] = $related_events;
    return view('frontend.event.event-details', $information);
}

  //applyCoupon
  public function applyCoupon(Request $request)
  {
    $coupon = Coupon::where('code', $request->coupon_code)->first();

    if (!$coupon) {
      Session::put('discount', NULL);
      return response()->json(['status' => 'error', 'message' => "Coupon is not valid"]);
    } else {

      $start = Carbon::parse($coupon->start_date);
      $end = Carbon::parse($coupon->end_date);
      $today = Carbon::now();
      $event = Session::get('event');
      $event_id = $event->id;
      $events = json_decode($coupon->events, true);
      if (!empty($events)) {
        if (in_array($event_id, $events)) {

          // if coupon is active
          if ($today->greaterThanOrEqualTo($start) && $today->lessThan($end)) {
            $value = $coupon->value;
            $type = $coupon->type;
            $early_bird_dicount = Session::get('total_early_bird_dicount');
            if ($early_bird_dicount != '') {
              $cartTotal = Session::get('sub_total') - $early_bird_dicount;
            } else {
              $cartTotal = Session::get('sub_total') - $early_bird_dicount;
            }
            if ($type == 'fixed') {
              $couponAmount = $value;
            } else {
              $couponAmount = ($cartTotal * $value) / 100;
            }
            $cartTotal - $couponAmount;
            Session::put('discount', $couponAmount);
            return response()->json(['status' => 'success', 'message' => "Coupon applied successfully"]);
          } else {
            return response()->json(['status' => 'error', 'message' => "Coupon is not valid"]);
          }
        } else {
          return response()->json(['status' => 'error', 'message' => "Coupon is not valid"]);
        }
      } else {
        // if coupon is active
        if ($today->greaterThanOrEqualTo($start) && $today->lessThan($end)) {
          $value = $coupon->value;
          $type = $coupon->type;
          $early_bird_dicount = Session::get('total_early_bird_dicount');
          if ($early_bird_dicount != '') {
            $cartTotal = Session::get('sub_total') - $early_bird_dicount;
          } else {
            $cartTotal = Session::get('sub_total') - $early_bird_dicount;
          }
          if ($type == 'fixed') {
            $couponAmount = $value;
          } else {
            $couponAmount = ($cartTotal * $value) / 100;
          }
          $cartTotal - $couponAmount;
          Session::put('discount', $couponAmount);
          return response()->json(['status' => 'success', 'message' => "Coupon applied successfully"]);
        } else {
          return response()->json(['status' => 'error', 'message' => "Coupon is not valid"]);
        }
      }
    }
  }

  //add_to_wishlist
  public function add_to_wishlist($id)
  {
    if (Auth::guard('customer')->check()) {
      $customer_id = Auth::guard('customer')->user()->id;
      $check = Wishlist::where('event_id', $id)->where('customer_id', $customer_id)->first();

      if (!empty($check)) {
        $notification = array('message' => 'You already added this event into your wishlist..!', 'alert-type' => 'error');
        return back()->with($notification);
      } else {
        $add = new Wishlist;
        $add->event_id = $id;
        $add->customer_id = $customer_id;
        $add->save();
        $notification = array('message' => 'Add to your wishlist successfully..!', 'alert-type' => 'success');
        return back()->with($notification);
      }
    } else {
      return redirect()->route('customer.login');
    }
  }



}
