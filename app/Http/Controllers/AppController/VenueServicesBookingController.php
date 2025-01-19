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
use App\Models\Venue\VenueServices;
use App\Models\Venue\VenueServicesBooking;
use App\Http\Controllers\AppController\VenueController;
use Carbon\Carbon;
use App\Models\Venue\VenueCoupon;



class VenueServicesBookingController extends Controller
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
            'service_id' => 'required|exists:venue_services,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $venueId = $validatedData['service_id'];
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

            $existingBooking = VenueServicesBooking::where('service_id', $venueId)
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
                'service_id' => 'required|exists:venue_services,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'guests' => 'nullable|integer|min:0',
                // 'coupon_code' => 'nullable|string|exists:venue_coupons,code',
            ]);

            // Fetch the venue
            $venue = VenueServices::findOrFail($validated['service_id']);

            // Combine start date and start time, end date and end time into DateTime objects
            $startDateTime = new \DateTime($validated['start_date'] . ' ' . $validated['start_time']);
            $endDateTime = new \DateTime($validated['end_date'] . ' ' . $validated['end_time']);

            // Calculate total hours of booking
            $totalHours = $endDateTime->diff($startDateTime)->h + ($endDateTime->diff($startDateTime)->days * 24);

            // Calculate total amount before discount
            $pricePerDay= $venue->price;
            $pricePerHour = $pricePerDay / 24; // Base price per hour
            $totalAmount = $pricePerHour * $totalHours;

            $adminCommission = $totalAmount * 0.10;
            $adminCommission = (int) round($adminCommission);
            $totalAmount += $adminCommission;
            $totalAmount = (int) round($totalAmount);

            $discount = 0;

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
            $booking = VenueServicesBooking::create(
            [
                'service_id' => $venue->id,
                'booking_id' => $bookingId,
                'customer_id' => $customer->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'guests' => $request->guests ?? 0,
                'status' => 'scheduled', 
                'description' => $request->description,
                'payment' => 'unpaid',
                'amount' => $totalAmount,
            ]);

            // Return success response
            return response()->json([
                'status' => 'true',
                'message' => 'Booking created successfully.',
                'data' => $booking,
                'details' => [
                    'total_amount' => $totalAmount,
                    'admin_commission' => $adminCommission,
                    'discount'=>$discount,
                    'total_hours' => $totalHours,
                ],
            ], 201);            

        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Failed to create booking: ' . $e->getMessage());
            // Handle exception and return error response
            return response()->json([
                'status' => 'false',
                'message' => 'Failed to create booking',
                'error' => $e->getMessage()
            ], 500); // HTTP 500 Internal Server Error
        }
    }

    

    public function getBookingsForCustomer(Request $request)
    {
        try {
            // Token validation for customer
            $user = $this->common->tokenValidation($request, 'customers');
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
            }
            
            $customerId = $user->id;

            // Fetch bookings for the customer
            $bookings = VenueBooking::with('venue:name,id,location')
                ->where('customer_id', $customerId)
                ->orderBy('start_date', 'asc')
                ->get();

            // Format the bookings
            $formattedBookings = $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'booking_id'=> $booking->booking_id,
                    'venue_name' => $booking->venue->name, // Get venue name from the relationship
                    'location' => $booking->venue->location,
                    'start_date' => $booking->start_date ? $booking->start_date->toDateString() : null,
                    'end_date' => $booking->end_date ? $booking->end_date->toDateString() : null,
                    'start_time' => $booking->start_time ? $booking->start_time->format('H:i') : null, // Format start time to hours and minutes
                    'end_time' => $booking->end_time ? $booking->end_time->format('H:i') : null, // Format end time to hours and minutes
                    'guests' => is_array($booking->guests) ? implode(', ', $booking->guests) : $booking->guests,
                    'status' => $booking->status,
                    'description' => $booking->description,
                    'payment' => $booking->payment,
                    'amount' => $booking->amount,
                ];
            });

            return response()->json([
                'success' => true,
                'bookings' => $formattedBookings,
            ], 200);

        } catch (\Exception $e) {
            // Log the exception for internal tracking
            \Log::error('Error fetching bookings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookings.',
                'error' => $e->getMessage(), // Include error message for debugging
            ], 500);
        }
    }


}
