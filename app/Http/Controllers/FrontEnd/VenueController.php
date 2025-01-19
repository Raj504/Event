<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Venue;
use App\Models\Venue\VenueBooking;
use App\Models\Venue\VenueImages;
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
use LDAP\Result;
use DB;
use App\Models\Venue\VenueCoupon;

class VenueController extends Controller
{
    public function venues(){
        
        $venues = Venue::all();
        $venueData = [];
        
        foreach ($venues as $venue) {
            $firstImage = VenueImages::where('venue_id', $venue->id)->orderBy('id')->first();
            $venueData[] = [
                'venue' => $venue,
                'first_image' => $firstImage ? $firstImage->image : null,
            ];
           
        }
        return view('frontend.Venue.index',compact('venueData'));
    }

    public function searchBySlug(Request $reqeuest)
    {
         $searchTerm=$reqeuest->slug;
        // $venues = Venue::where('name', $reqeuest->slug)->orWhere('location', 'specific location')->get();
        $venues = Venue::where(function ($query) use ($searchTerm) {
            $query->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%")->orWhere('price','like',"%{$searchTerm}");
        })->get();
        $ids = $venues->pluck('id');

        if(!$venues){
            return redirect()->back()->with('error', 'No venue found.');
        }
        $venueData = [];
        foreach ($venues as $venue) {
            $firstImage = VenueImages::whereIn('venue_id', $ids)->orderBy('id')->first();
            $venueData[] = [
                'venue' => $venue,
                'first_image' => $firstImage ? $firstImage->image : null,
            ];
        }
      
        return view('frontend.Venue.index', compact('venueData'));
    }

    public function searchByDate(Request $request){
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        
        $venueIds = DB::table('venue_bookings')
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('payment', 'unpaid')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereDate('start_date', $startDate)
                      ->orWhereDate('end_date', $endDate)
                      ->orWhereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($query) use ($startDate, $endDate) {
                          $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                      });
            })
            ->pluck('venue_id')->toArray();
        
        $venues=Venue::whereIn('id', $venueIds)->get();
        $ids = $venues->pluck('id');
        $venueData = [];
        foreach ($venues as $venue) {
            $firstImage = VenueImages::whereIn('venue_id', $ids)->orderBy('id')->first();
            $venueData[] = [
                'venue' => $venue,
                'first_image' => $firstImage ? $firstImage->image : null,
            ];
        }
     
        // exit;
        return view('frontend.Venue.index', compact('venueData'));

    }
      public function venueDetails($id){
        $data = Venue::find($id);
      return view('frontend.Venue.venueDetails',compact('data'));
}
   public function venueDetails1Store(Request $request){

        $request->validate([
            'start_date'=>'required',
            'end_date'=>'required',
            'start_time'=>'required',
            'end_time'=>'required',
            'guests'=>'required',
            'description'=>'required',
            ]);

        $venue = Venue::findOrFail($request->venue_id);
        if (!empty($venue)) {
          
            $couponCode = $request->input('coupon');
            $coupon = VenueCoupon::where('code', $couponCode)->first();
        
            if ($coupon && $coupon->isValid()) {
                $totalAmount=$request->input('payment');
                $discount = ($totalAmount * $coupon->discount_percentage) / 100;
                $totalAmount -= $discount;
                $totalAmount = (int) round($totalAmount);
            } else {
               // HTTP 400 Bad Request
               $totalAmount=$request->input('payment');
            }
        $venues=new VenueBooking;
        $guest=[
            'veg_guests' => $request->veg_guests ?? 0, // default to 0 if not provided
            'non_veg_guests' => $request->non_veg_guests ?? 0, // default to 0 if not provided
        ];
        $booking_id=rand(10000000, 99999999);
        $venues->booking_id=$booking_id;
        $venues->start_date=$request->input('start_date');
        $venues->end_date=$request->input('end_date');
        $venues->start_time=$request->input('start_time');
        $venues->end_time=$request->input('end_time');
        $venues->guests= $guest;
        $venues->description=$request->input('description');
        $venues->venue_id=$request->input('venue_id');
        $venues->payment="unpaid";
        $venues->status="scheduled";
        $venues->amount=$totalAmount;
        $venues->customer_id =Auth::guard('customer')->id();

        $venues->save();
        return back()->with('success','Booking Confirmed Successfully');
    }
        else{
            return back()->with('success','Booking not Confirmed ');
        }
    }

    public function venueBookingShow()
    { 
        $customerId = Auth::guard('customer')->id();
        $bookingshow = VenueBooking::where('customer_id', $customerId)
                                    ->orderBy('created_at', 'desc')  
                                    ->get();
        $venueId = VenueBooking::with('venue')
                     ->where('customer_id', $customerId)
                    ->orderBy('created_at', 'desc')  
                    ->pluck('venue_id')
                    ->first();
        $venueData=Venue::where('id',$venueId)->get();
        return view('frontend.Venue.venuebookingshow', compact('bookingshow','venueData'));

    }

      public function venueCustomerDetails($id){
        
    $booking = VenueBooking::find($id);
    // dd($bookings);
     return view('frontend.customer.dashboard.customerdetails',compact('booking'));
  }
    
}