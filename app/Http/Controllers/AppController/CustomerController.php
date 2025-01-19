<?php

namespace App\Http\Controllers\AppController;

use App\Http\Controllers\Controller; // Ensure you're extending the base Controller
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\OrganizerInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Helpers\Common;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Venue\VenueCategory;
use Illuminate\Support\Facades\File;
use App\Models\Venue\VenueServicesBooking;
use App\Models\Venue\VenueBooking;
use Barryvdh\DomPDF\Facade\Pdf;


class CustomerController extends Controller
{   private $common;

    public function __construct()
    {
        $this->common = new Common();
    }

    public function generateInvoice($bookingId)
    {
        try {
            // Fetch the booking details
            $booking = VenueBooking::with(['customer', 'venue', 'installments'])
                ->findOrFail($bookingId);


            $venueBooking = VenueBooking::with('customer', 'organizer')->find($bookingId);

            if (!$venueBooking) {
                return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
            }

            // Retrieve related user and organizer data
            $user = $venueBooking->customer;
            $organizer = $venueBooking->organizer;
            $guests = $venueBooking->guests; // Assuming 'guests' is an array or collection in your model

            // Pass all data to the view
            return view('venue_booking', compact('venueBooking', 'user', 'organizer', 'guests'));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generateInvoiceForService($bookingId)
{
    try {
        // Fetch the service booking details
        $venueBooking = VenueServicesBooking::with(['customer', 'service', 'installments'])
            ->findOrFail($bookingId);

        if (!$venueBooking) {
            return response()->json(['success' => false, 'message' => 'Service booking not found.'], 404);
        }

        // Retrieve related user and service organizer data
        $user = $venueBooking->customer; // Assuming 'customer' is a relation in the model
        $organizer = $venueBooking->organizer; // Assuming 'organizer' is a relation in the model
        $services = $venueBooking->services; // Assuming 'services' is an array or collection in your model
        $guests = $venueBooking->guests;

        // Pass all data to the view
        return view('venue_booking', compact('venueBooking', 'user', 'organizer', 'guests'));
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to generate service invoice.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function signUp(Request $request)
    {   
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'phone' => 'required|digits_between:10,13|unique:organizers,phone',
                'email' => 'required|email|unique:organizers,email',
                'password' => 'required|string|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $names = explode(' ', $request->name);
            $fname = $names[0];
            $lname = count($names) > 1 ? implode(' ', array_slice($names, 1)) : '';

            $username = strtolower($fname);

            $customer = Customer::create([
                'first_name' => $fname,
                'last_name' => $lname,
                'username' => $username,
                'email' => $request->email,
                'contact_number' => $request->phone,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
                'status' => 1,
            ]);

            $customer->refresh();
            
            return response()->json([
                'status' => true,
                'message' => 'Venue Customer Profile Created Successfully!!',
                'Customer_id' => $customer,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {   
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $customer = Customer::where('email', $request->email)->first();

            if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        $customer->api_token = Str::random(60);
        $customer->save();

        return response()->json([
            'message' => 'Login successful',
            'customer' => $customer,
            'api_token' => $customer->api_token
        ], 200);
        }
        catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
                ], 500);
        }
    }

    public function update(Request $request)
{
    // Authorization check
    $customer = $this->authorizeCustomer($request);
    if (!$customer) {
        return $this->errorResponse('Unauthorized: Invalid API token', 401);
    }

    // Validation rules
    $validator = Validator::make($request->all(), [
        'fname' => 'nullable|string',
        'lname' => 'nullable|string',
        'username' => 'nullable|string',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Validation for image
        'phone' => 'nullable|numeric',
        'address' => 'nullable|string',
        'country' => 'nullable|string',
        'state' => 'nullable|string',
        'city' => 'nullable|string',
        'zip_code' => 'nullable|string',
        'password' => 'nullable|string',
        'upi' => 'nullable|string',
        'address' => 'nullable|string',
        'city' => 'nullable|string',
        'state' => 'nullable|string',
        'country' => 'nullable|string',
        'zip_code' => 'nullable|digits_between:6,6',
    ]);

    // Handle validation errors
    if ($validator->fails()) {
        return $this->errorResponse($validator->errors()->first(), 422);
    }

    $existingPhotoUrl = $customer->photo ? asset('images/customer/' . $customer->photo) : null;

     // Handle profile picture upload
    if ($request->hasFile('photo')) {
        $image = $request->file('photo');
        $extension = $image->getClientOriginalExtension();
        $path = 'images/customer'; // Directory path relative to public
        $endName = 'customer'; // Prefix for the filename
        $imageDirectory = public_path($path);

        // Create directory if not exists
        if (!File::isDirectory($imageDirectory)) {
            File::makeDirectory($imageDirectory, 0777, true, true);
        }

        $imageName = $endName . uniqid() . '.' . $extension;
        $image->move($imageDirectory, $imageName);

        // Update the profile picture path
        $imagePath = $path . '/' . $imageName;
        $imageUrl = asset($imagePath);
        $customer->image = $imageUrl;
    } else {
        // If no new photo is uploaded, use the existing photo URL
        $imageUrl = $existingPhotoUrl;
    }

    if($request->has('password')){
        $customer->password =Hash::make($request->password);
    }

    // Update customer details
    try {
        $customer->fill($request->except('image', 'password'))->save();

        return response()->json([
            'status' => true,
            'message' => 'Customer updated successfully',
            'data' => [
                'customer' => $customer,
                'photo_url' => $imageUrl
            ]
        ], 200);
    } catch (\Exception $e) {
        return $this->errorResponse('Failed to update customer: ' . $e->getMessage(), 500);
    }
}

    private function authorizeCustomer(Request $request)
    {
        return Customer::where('api_token', $request->bearerToken())->first();
    }

    private function errorResponse($message, $statusCode)
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $statusCode);
    }

    private function successResponse($message, $data = null)
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, 200);
    }
    
    public function getCustomer(Request $request)
    {
        $user = $this->common->tokenValidation($request, 'customers');
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
            }
        // Validate and get the customer based on the token
        $customer = $user;
        
        if (!$customer) {
            // If no customer is found, return a not found response
            return response()->json(['status' => false, 'message' => 'Customer Not Found'], 404);
        }

        return response()->json([
            'status' => true,
            'customer' => $customer,
            'photo_url' => $customer->photo ? asset('images/customer/' . $customer->photo) : null // Return photo URL
        ], 200);
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
    
            // Fetch venue bookings for the customer with related venue and installments
            $venueBookings = VenueBooking::with(['venue:name,id,location', 'installments'])
                ->where('customer_id', $customerId)
                ->orderBy('created_at', 'desc')
                ->get();
    
            // Fetch service bookings for the customer with installments
            $serviceBookings = VenueServicesBooking::with(['service:name,id', 'installment_services'])
                ->where('customer_id', $customerId)
                ->orderBy('created_at', 'desc')
                ->get();
    
            // Format the venue bookings
            $formattedVenueBookings = $venueBookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'booking_id' => $booking->booking_id,
                    'venue_name' => $booking->venue->name ?? null,
                    'location' => $booking->venue->location ?? null,
                    'start_date' => $booking->start_date ? $booking->start_date->toDateString() : null,
                    'end_date' => $booking->end_date ? $booking->end_date->toDateString() : null,
                    'start_time' => $booking->start_time ? $booking->start_time->format('H:i') : null,
                    'end_time' => $booking->end_time ? $booking->end_time->format('H:i') : null,
                    'guests' => is_array($booking->guests) ? implode(', ', $booking->guests) : (string)$booking->guests,
                    'status' => $booking->status,
                    'description' => $booking->description,
                    'payment' => $booking->payment,
                    'amount' => $booking->amount,
                    'installments' => $booking->installments ? $booking->installments->map(function ($installment) {
                        return [
                            'id' => $installment->id,
                            'amount' => $installment->amount,
                            'status' => $installment->status,
                        ];
                    }) : [],
                ];
            });
            
    
            // Format the service bookings
            $formattedServiceBookings = $serviceBookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'booking_id' => $booking->booking_id,
                    'service_name' => $booking->service->name ?? null,
                    'start_date' => $booking->start_date ? $booking->start_date->toDateString() : null,
                    'end_date' => $booking->end_date ? $booking->end_date->toDateString() : null,
                    'start_time' => $booking->start_time ? $booking->start_time->format('H:i') : null,
                    'end_time' => $booking->end_time ? $booking->end_time->format('H:i') : null,
                    'guests' => is_array($booking->guests) ? implode(', ', $booking->guests) : (string)$booking->guests,
                    'status' => $booking->status,
                    'description' => $booking->description,
                    'payment' => $booking->payment,
                    'amount' => $booking->amount,
                    'installments' => $booking->installment_services ? $booking->installment_services->map(function ($installment) {
                        return [
                            'id' => $installment->id,
                            'amount' => $installment->amount,
                            'status' => $installment->status,
                        ];
                    }) : [],
                ];
            });
            
    
            // Combine both venue and service bookings
            $bookings = [
                'venue' => $formattedVenueBookings,
                'service' => $formattedServiceBookings,
            ];
    
            return response()->json([
                'success' => true,
                'bookings' => $bookings,
            ], 200);
    
        } catch (\Exception $e) {
            // Log the exception for internal tracking
            \Log::error('Error fetching bookings: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookings.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancelBooking(Request $request)
    {
        // Validate the token and retrieve the organizer
        $user = $this->common->tokenValidation($request, 'customers');
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $customer = $user;

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer|exists:venue_bookings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // Find the booking
            $booking = VenueBooking::findOrFail($request->input('booking_id'));

            if ($booking->customer_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to cancel this booking.',
                ], 403); // Forbidden
            }

            // Check if the booking is already canceled
            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking is already canceled.',
                    'booking' => $booking,
                ], 400);
            }

            // Check if the booking is already canceled
            if ($booking->status === 'completed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking is already completed.',
                    'booking' => $booking,
                ], 400);
            }

            // Update the booking status to canceled
            $booking->status = 'cancelled';
            $booking->save();

            return response()->json([
                'success' => true,
                'message' => 'Booking canceled successfully.',
                'booking' => $booking,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}
