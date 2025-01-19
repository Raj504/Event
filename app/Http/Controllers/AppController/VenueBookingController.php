<?php

namespace App\Http\Controllers\AppController;

use App\Http\Controllers\Controller; // Ensure you're extending the base Controller
use Illuminate\Http\Request;
use App\Models\Organizer;
use App\Models\OrganizerInfo;
use App\Models\OrganizerImage;
use App\Models\Venue\Banners;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Helpers\Common;
use Illuminate\Support\Str;
use App\Models\Venue\Venue;
use App\Models\Venue\VenueBooking;
use App\Models\Venue\VenueType;
use App\Models\Venue\VenueReview;
use App\Models\Venue\VenueCategory;
use App\Models\Venue\VenueImages;
use App\Models\Venue\VenueCoupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;


class VenueBookingController extends Controller
{   private $common;

    public function __construct()
    {
        $this->common = new Common();
    }
    public function getAvailableSlots(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'venue_id' => 'required|exists:venues,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $venueId = $validatedData['venue_id'];
            $startDate = Carbon::createFromFormat('Y-m-d', $validatedData['start_date']);
            $endDate = Carbon::createFromFormat('Y-m-d', $validatedData['end_date']);

            // Generate 6-hour slots for each day within the date range
            $slots = [];
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                for ($hour = 0; $hour < 24; $hour += 6) {
                    $startTime = $currentDate->copy()->setTime($hour, 0);
                    $endTime = $startTime->copy()->addHours(6);

                    $slots[] = [
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ];
                }
                $currentDate->addDay();
            }

            $availableSlots = [];

            foreach ($slots as $slot) {
                $startTime = $slot['start_time'];
                $endTime = $slot['end_time'];

                $existingBooking = VenueBooking::where('venue_id', $venueId)
                    ->whereIn('status', ['confirmed']) // Exclude slots based on multiple statuses
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->where(function ($query) use ($startTime, $endTime) {
                            $query->where('start_time', '<', $endTime)
                                ->where('end_time', '>', $startTime);
                        });
                    })
                    ->exists();

                if (!$existingBooking) {
                    $availableSlots[] = [
                        'date' => $startTime->toDateString(),
                        'start_time' => $startTime->format('H:i'),
                        'end_time' => $endTime->format('H:i'),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'available_slots' => $availableSlots
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            // Log the exception for internal tracking
            \Log::error('Fetching available slots failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching available slots.',
                'error' => $e->getMessage() // Optionally include this for more detailed error info
            ], 500);
        }
    }
    public function createBooking(Request $request)
    {
        $user = $this->common->tokenValidation($request, 'customers');
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $customer = $user;

        try {
            // Validate the request data
            $validated = $request->validate([
                'venue_id' => 'required|exists:venues,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'guests' => 'required|integer|min:0',
                'veg_guests' => 'nullable|integer|min:0',
                'non_veg_guests' => 'nullable|integer|min:0',
                'coupon_code' => 'nullable|string|exists:venue_coupons,code', // Optional coupon code
            ]);

            // Fetch the venue
            $venue = Venue::findOrFail($validated['venue_id']);

            // Check guest capacity
            if (
                $validated['guests'] > $venue->capacity ||
                ($validated['veg_guests'] + $validated['non_veg_guests']) > $venue->capacity
            ) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Number of guests exceeds the venue capacity.',
                ], 400);
            }

            // Combine start date and start time, end date and end time into DateTime objects
            $startDateTime = new \DateTime($validated['start_date'] . ' ' . $validated['start_time']);
            $endDateTime = new \DateTime($validated['end_date'] . ' ' . $validated['end_time']);

            // Calculate total hours of booking
            $totalHours = $endDateTime->diff($startDateTime)->h + ($endDateTime->diff($startDateTime)->days * 24);

            // Calculate total amount before discount
            $pricePerDay= $venue->price;
            $pricePerHour = $pricePerDay / 24; // Base price per hour
            $totalAmount = $pricePerHour * $totalHours;

            $cateringPrice =  ($request->veg_guests* $venue->veg_price)+ ($request->non_veg_guests* $venue->non_veg_price);
            $totalAmount = $totalAmount + $cateringPrice;
            

            $adminCommission = $totalAmount * 0.10;
            $adminCommission = (int) round($adminCommission);
            $totalAmount += $adminCommission;
            $totalAmount = (int) round($totalAmount);
            $discount = 0;

            // dd($pricePerDay, $pricePerHour, $totalHours, $totalAmount, );

            //dd($totalAmount, $venue->price,  $pricePerHour, $totalHours,  $validated['guests'], $adminCommission, $cateringPrice);


            // Check and apply coupon discount if provided
            if (!empty($validated['coupon_code'])) {
                $coupon = VenueCoupon::where('code', $validated['coupon_code'])->first();

                if ($coupon && $coupon->isValid()) {
                    // Apply the discount
                    $discount = ($totalAmount * $coupon->discount_percentage) / 100;
                    $totalAmount -= $discount;
                    $totalAmount = (int) round($totalAmount);
                } else {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'Invalid or expired coupon code.',
                    ], 400); // HTTP 400 Bad Request
                }
            }

            // Generate a unique booking ID
            $bookingId = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);

            // Create the booking
            $booking = VenueBooking::create([
                'venue_id' => $venue->id,
                'organizer_id' => $venue->organizer_id,
                'booking_id' => $bookingId,
                'customer_id' => $customer->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'guests' => [
                    'veg_guests' => ($request->veg_guests === 0 && $request->non_veg_guests === 0) ? $request->guests : ($request->veg_guests ?? $request->guests), 
                    'non_veg_guests' => $request->non_veg_guests ?? 0, // default to 0 if not provided
                        ],
                'status' => 'scheduled', 
                'description' => $request->description,
                'payment' => 'unpaid',
                'amount' => $totalAmount,
            ]);

            $booking = VenueBooking::with('user')->find($booking->id);

            try {
                $user = $booking->customer;
            
                Mail::send('booking_confirmation', ['booking' => $booking], function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                            ->subject('Booking Confirmation');
                });
            
            } catch (\Exception $e) {
                \Log::error('Mail sending failed: ' . $e->getMessage());
            }

            // Return success response
            return response()->json([
                'status' => 'true',
                'message' => 'Booking created successfully.',
                'data' => $booking,
                'details' => [
                    'total_amount' => $totalAmount,
                    'admin_commission' => $adminCommission,
                    'discount'=>$discount,
                    'catering_price' => $cateringPrice,
                    'total_hours' => $totalHours,
                ],
            ], 201);            

        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Failed to create booking: ' . $e->getMessage());

            // Handle exception and return error response
            return response()->json([
                'status' => 'false',
                'message' => 'Failed to create booking.',
                'error' => $e->getMessage()
            ], 500); // HTTP 500 Internal Server Error
        }
    }
}
