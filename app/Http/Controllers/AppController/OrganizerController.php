<?php

namespace App\Http\Controllers\AppController;

use App\Http\Controllers\Controller; // Ensure you're extending the base Controller
use Illuminate\Http\Request;
use App\Models\Organizer;
use App\Models\OrganizerInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Helpers\Common;
use Illuminate\Support\Str;
use App\Models\Venue\VenueCategory;
use App\Models\Venue\Venue;
use App\Models\Venue\VenueImage;
use App\Models\Venue\VenueBooking;
use App\Models\Venue\VenueServices;
use App\Models\Venue\VenueServicesBooking;
use Illuminate\Support\Facades\File;

class OrganizerController extends Controller
{   private $common;

    public function __construct()
    {
        $this->common = new Common();
    }

    public function signUp(Request $request)
    {   
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'shop_name' => 'nullable|string',
                'phone' => 'required|digits_between:10,13|unique:organizers,phone',
                'email' => 'required|email|unique:organizers,email',
                'password' => 'required|string',
                'gstin' => 'nullable|string',
                'uin' => 'nullable|string',
                'pan' => 'nullable|string',
                'aadhar' => 'nullable|string',
                'account_holder_name' => 'nullable|string',
                'account_number' => 'nullable|string',
                'ifsc' => 'nullable|string',
                'branch' => 'nullable|string',
                'bank' => 'nullable|string',
                'upi' => 'nullable|string',
                'location' => 'nullable|array', // Ensure location is an array
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $username = substr($request->email, 0, strpos($request->email, '@'));

            // Extract location details from request
            $location = $request->location;
            $country = $city = $state = $zipcode = $address = '';

            if (isset($location['address_components'])) {
                foreach ($location['address_components'] as $component) {
                    if (in_array('country', $component['types'])) {
                        $country = $component['long_name'];
                    }
                    if (in_array('locality', $component['types'])) {
                        $city = $component['long_name'];
                    }
                    if (in_array('administrative_area_level_1', $component['types'])) {
                        $state = $component['long_name'];
                    }
                    if (in_array('postal_code', $component['types'])) {
                        $zipcode = $component['long_name'];
                    }
                    if (in_array('street_number', $component['types']) || in_array('route', $component['types']) || in_array('neighborhood', $component['types']) || in_array('sublocality', $component['types']) || in_array('establishment', $component['types'])) {
                        $address .= $component['long_name'] . ', ';
                    }
                }
                $address = rtrim($address, ', ');
            }
                
            //dd($country, $city, $state, $zipcode, $address);
            // Create organizer user
            $user = Organizer::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'username' => $username,
                'email_verified_at' => now(),
                'status' => 1,
            ]);

            $user->refresh();

            // Create organizer info
            $userinfo = OrganizerInfo::create([
                'organizer_id' => $user->id,
                'name' => $request->name,
                'shop_name' => $request->shop_name,
                'designation' => 'Organizer',
                'gstin' => $request->gstin,
                'uin' => $request->uin,
                'pan' => $request->pan,
                'aadhar' => $request->aadhar,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'ifsc' => $request->ifsc,
                'branch' => $request->branch,
                'bank' => $request->bank,
                'upi' => $request->upi,
                'country' => $country,
                'city' => $city,
                'state' => $state,
                'zip_code' => $zipcode,
                'address' => $address,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Venue Seller Profile Created Successfully!!',
                'organizer_id' => $user->id
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

            // Attempt to find the organizer by email
            $organizer = Organizer::where('email', $request->email)->first();

            if (!$organizer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email not found'
                ], 404);
            }

            // Check if the password matches
            if (!Hash::check($request->password, $organizer->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Incorrect password'
                ], 401);
            }

            // Generate a new API token
            $api_token = Str::random(60);
            $organizer->api_token = $api_token;
            $organizer->save();

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'api_token' => $api_token,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function checkemail(Request $request){

        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Attempt to find the organizer by email
            $organizer = Organizer::where('email', $request->email)->first();

            if (!$organizer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email not found'
                ], 400);
            }

            return response()->json([
                'status' => true,
                'message' => 'Email Found',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request)
    {
        // Authorization check
        $organizer = $this->authorizeOrganizer($request);
        if (!$organizer) {
            return $this->errorResponse('Unauthorized: Invalid API token', 401);
        }
    
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'phone' => 'nullable|digits_between:10,13|unique:organizers,phone,' . $organizer->id,
            'gstin' => 'nullable|string',
            'uin' => 'nullable|string',
            'pan' => 'nullable|string',
            'aadhar' => 'nullable|string',
            'account_holder_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'ifsc' => 'nullable|string',
            'branch' => 'nullable|string',
            'bank' => 'nullable|string',
            'upi' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'zip_code' => 'nullable|numeric',
        ]);
    
        // Handle validation errors
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }
    
        // Store the current photo URL
        $existingPhotoUrl = $organizer->photo ? asset('images/organizer/' . $organizer->photo) : null;
    
        // Handle file upload if a new photo is provided
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $extension = $image->getClientOriginalExtension();
            $path = 'images/organizer'; // Directory path relative to public
            $endName = 'organizer'; // Prefix for the filename
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
            $organizer->photo = $imageName;
        } else {
            // If no new photo is uploaded, use the existing photo URL
            $imageUrl = $existingPhotoUrl;
        }
    
        // Update organizer details
        try {
            // Update Organizer details
            $organizer->fill($request->except('photo'))->save();
    
            // Find the related OrganizerInfo and update attributes
            $organizerInfo = OrganizerInfo::where('organizer_id', $organizer->id)->first();
    
            if ($organizerInfo) {
                // Update each OrganizerInfo attribute separately
                $organizerInfo->name = $request->name;
                $organizerInfo->gstin = $request->gstin;
                $organizerInfo->uin = $request->uin;
                $organizerInfo->pan = $request->pan;
                $organizerInfo->aadhar = $request->input('aadhar', $organizerInfo->aadhar);
                $organizerInfo->account_holder_name = $request->input('account_holder_name', $organizerInfo->account_holder_name);
                $organizerInfo->account_number = $request->input('account_number', $organizerInfo->account_number);
                $organizerInfo->ifsc = $request->input('ifsc', $organizerInfo->ifsc);
                $organizerInfo->branch = $request->input('branch', $organizerInfo->branch);
                $organizerInfo->bank = $request->input('bank', $organizerInfo->bank);
                $organizerInfo->upi = $request->input('upi', $organizerInfo->upi);
                $organizerInfo->address = $request->input('address', $organizerInfo->address);
                $organizerInfo->city = $request->input('city', $organizerInfo->city);
                $organizerInfo->state = $request->input('state', $organizerInfo->state);
                $organizerInfo->country = $request->input('country', $organizerInfo->country);
                $organizerInfo->zip_code = $request->input('zip_code', $organizerInfo->zip_code);
    
                $organizerInfo->save();
            }

            $mergedData = array_merge($organizer->toArray(), $organizerInfo ? $organizerInfo->toArray() : []);
            // Add the photo URL
            $mergedData['photo_url'] = $imageUrl;
    
            return response()->json([
                'status' => true,
                'message' => 'Organizer updated successfully',
                'data' => [
                    'organizer' => $mergedData,
                ]
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update organizer: ' . $e->getMessage(), 500);
        }
    }
    
    
    
    private function authorizeOrganizer(Request $request)
    {
        return Organizer::where('api_token', $request->bearerToken())->first();
    }

    /**
     * Create a JSON error response.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    private function errorResponse($message, $statusCode)
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $statusCode);
    }

    /**
     * Create a JSON success response.
     *
     * @param  string  $message
     * @param  mixed  $data
     * @return \Illuminate\Http\JsonResponse
     */
    private function successResponse($message, $data = null)
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['organizer'] = $data;
        }

        return response()->json($response, 200);
    }
 
 
    public function getOrganizer(Request $request)
    {
        $user = $this->common->tokenValidation($request, 'organizers');
        
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            // Return error response if token validation fails
            return $user;
        }

        if (!$user) {
            // If no user is found, return a not found response
            return response()->json(['status' => false, 'message' => 'User Not Found'], 404);
        }

        // Fetch additional details from organizerinfo table
        $organizerInfo = OrganizerInfo::where('organizer_id', $user->id)->first();
        $existingPhotoUrl = $user->photo ? asset('images/organizer/' . $user->photo) : null;

        // Combine organizer details with organizer info
        $response = [
            'status' => true,
            'organizer' => $user,
            'photo_url' => $existingPhotoUrl,
        ];

        if ($organizerInfo) {
            $response['organizer_info'] = $organizerInfo;
        } else {
            $response['organizer_info'] = null; // or some default message
        }

        return response()->json($response, 200);
    }

    public function getVenueBookingsForOrganizer(Request $request)
    {
        try {
            // Validate the token and get the organizer
            $user = $this->common->tokenValidation($request, 'organizers');
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
            }
    
            // Fetch the organizer's venues
            $venues = Venue::where('organizer_id', $user->id)->pluck('id');
    
            if ($venues->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No venues found for this organizer.',
                ], 404);
            }
    
            // Fetch bookings for the venues owned by the organizer
            $bookings = VenueBooking::with([
                'venue:name,id',
                'customer:username,id,contact_number,email',
                'installments:id,booking_id,amount,status,updated_at'
            ])
                ->whereIn('venue_id', $venues)
                ->orderBy('created_at', 'desc')
                ->get();
    
            // Format the bookings
            $formattedBookings = $bookings->map(function ($booking) {
                $vegGuests = $booking->guests['veg_guests'] ?? 0;
                $nonVegGuests = $booking->guests['non_veg_guests'] ?? 0;
                $guestsFormatted = "{$vegGuests} veg, {$nonVegGuests} non-veg";
    
                return [
                    'id' => $booking->id,
                    'booking_id' => $booking->booking_id,
                    'venue_name' => $booking->venue->name,
                    'start_date' => $booking->start_date ? $booking->start_date->toDateString() : null,
                    'end_date' => $booking->end_date ? $booking->end_date->toDateString() : null,
                    'start_time' => $booking->start_time ? $booking->start_time->format('H:i') : null,
                    'end_time' => $booking->end_time ? $booking->end_time->format('H:i') : null,
                    'guests' => $guestsFormatted,
                    'status' => $booking->status,
                    'description' => $booking->description,
                    'payment' => $booking->payment,
                    'amount' => $booking->amount,
                    'paid_amount' => $booking->paid_amount ? $booking->paid_amount : 0,
                    'customer_name' => $booking->customer ? $booking->customer->username : 'Unknown',
                    'customer_phone' => $booking->customer ? $booking->customer->phone : 'Unknown',
                    'customer_email' => $booking->customer ? $booking->customer->email : 'Unknown',
                    'installments' => $booking->installments->map(function ($installment) {
                        return [
                            'id' => $installment->id,
                            'amount' => $installment->amount,
                            'updated_at' => $installment->updated_at,
                            'status' => $installment->status,
                        ];
                    }),
                ];
            });
    
            return response()->json([
                'success' => true,
                'bookings' => $formattedBookings,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching venue bookings for organizer: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch venue bookings.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function getServiceBookingsForOrganizer(Request $request)
    {
        try {
            // Validate the token and get the organizer
            $user = $this->common->tokenValidation($request, 'organizers');
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
            }
    
            // Fetch the organizer's services
            $services = VenueServices::where('organizer_id', $user->id)->pluck('id');
    
            if ($services->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No bookings for this organizer.',
                ], 404);
            }
    
            // Fetch bookings for the services owned by the organizer
            $bookings = VenueServicesBooking::with([
                'service:name,id',
                'customer:username,id,contact_number,email',
                'installments:id,booking_id,amount,updated_at,status'
            ])
                ->whereIn('service_id', $services)
                ->orderBy('created_at', 'desc')
                ->get();
    
            // Format the bookings
            $formattedBookings = $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'booking_id' => $booking->booking_id,
                    'service_name' => $booking->service ? $booking->service->name : 'Unknown',
                    'start_date' => $booking->start_date ? $booking->start_date->toDateString() : null,
                    'end_date' => $booking->end_date ? $booking->end_date->toDateString() : null,
                    'start_time' => $booking->start_time ? $booking->start_time->format('H:i') : null,
                    'end_time' => $booking->end_time ? $booking->end_time->format('H:i') : null,
                    'guests' => $booking->guests,
                    'status' => $booking->status,
                    'description' => $booking->description,
                    'payment' => $booking->payment,
                    'amount' => $booking->amount,
                    'customer_name' => $booking->customer ? $booking->customer->username : 'Unknown',
                    'customer_phone' => $booking->customer ? $booking->customer->phone : 'Unknown',
                    'customer_email' => $booking->customer ? $booking->customer->email : 'Unknown',
                    'installments' => $booking->installments->map(function ($installment) {
                        return [
                            'id' => $installment->id,
                            'amount' => $installment->amount,
                            'updated_at' => $installment->updated_at,
                            'status' => $installment->status,
                        ];
                    }),
                ];
            });
    
            return response()->json([
                'success' => true,
                'bookings' => $formattedBookings,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching service bookings for organizer: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch service bookings.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    


    public function confirmBooking(Request $request)
    {   
        $user = $this->common->tokenValidation($request, 'organizers');
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
        }

        $organizer = $user;

        // Validate the request
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

            
            // Check if the booking is associated with a venue owned by the organizer
            $venue = Venue::findOrFail($booking->venue_id);

            // if ($venue->organizer_id !== $organizer->id) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'You do not have permission to confirm this booking.',
            //     ], 403); // Forbidden
            // }
            // Check if the booking is already confirmed
            if ($booking->status === 'confirmed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking is already confirmed.',
                    'booking' => $booking,
                ], 400);
            }

            // Update the booking status to confirmed
            $booking->status = 'confirmed';
            $booking->save();

            return response()->json([
                'success' => true,
                'message' => 'Booking confirmed successfully.',
                'booking' => $booking,
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function confirmBookingService(Request $request)
    {   
        $user = $this->common->tokenValidation($request, 'organizers');
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
        }

        $organizer = $user;

        // Validate the request
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer|exists:venue_services_bookings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // Find the booking
            $booking = VenueServicesBooking::findOrFail($request->input('booking_id'));

            
            // Check if the booking is associated with a venue owned by the organizer
            $venue = VenueServices::findOrFail($booking->service_id);

            // if ($venue->organizer_id !== $organizer->id) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'You do not have permission to confirm this booking.',
            //     ], 403); // Forbidden
            // }
            // Check if the booking is already confirmed
            if ($booking->status === 'confirmed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking is already confirmed.',
                    'booking' => $booking,
                ], 400);
            }

            // Update the booking status to confirmed
            $booking->status = 'confirmed';
            $booking->save();

            return response()->json([
                'success' => true,
                'message' => 'Booking confirmed successfully.',
                'booking' => $booking,
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function toggleStatus(Request $request)
    {   
        $user = $this->common->tokenValidation($request, 'organizers');
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:venues,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $venue = Venue::findOrFail($request->id);

        // Check if the authenticated user is the organizer who created the venue
        if ($user->id !== $venue->organizer_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to update this venue.'
            ], 403); // HTTP 403 Forbidden
        }
        
        $venue->status = $venue->status === 1 ? 0 : 1;

        $venue->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Venue status updated successfully',
            'venue' => $venue
        ]);
    }

    public function toggleServiceStatus(Request $request)
    {   
        $user = $this->common->tokenValidation($request, 'organizers');
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:venue_services,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $venue = VenueServices::findOrFail($request->id);

        // Check if the authenticated user is the organizer who created the venue
        // if ($user->id !== $venue->organizer_id) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'You are not authorized to update this venue.'
        //     ], 403); // HTTP 403 Forbidden
        // }
        
        $venue->status = $venue->status === 1 ? 0 : 1;

        $venue->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Venue status updated successfully',
            'venue' => $venue
        ]);
    }

    public function cancelBooking(Request $request)
    {
        // Validate the token and retrieve the organizer
        $user = $this->common->tokenValidation($request, 'organizers');
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $organizer = $user;

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

            // Check if the booking is associated with a venue owned by the organizer
            $venue = Venue::findOrFail($booking->venue_id);

            if ($venue->organizer_id !== $organizer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to cancel this booking.',
                ], 403); // Forbidden
            }

            // Check if the booking is already canceled
            if ($booking->status === 'canceled') {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking is already canceled.',
                    'booking' => $booking,
                ], 400);
            }

            // Update the booking status to canceled
            $booking->status = 'canceled';
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

    public function cancelBookingService(Request $request)
    {
        // Validate the token and retrieve the organizer
        $user = $this->common->tokenValidation($request, 'organizers');
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $organizer = $user;

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer|exists:venue_services_bookings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // Find the booking
            $booking = VenueServicesBooking::findOrFail($request->input('booking_id'));

            // Check if the booking is associated with a venue owned by the organizer
            $venue = VenueServices::findOrFail($booking->service_id);

            if ($venue->organizer_id !== $organizer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to cancel this booking.',
                ], 403); // Forbidden
            }

            // Check if the booking is already canceled
            if ($booking->status === 'canceled') {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking is already canceled.',
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
