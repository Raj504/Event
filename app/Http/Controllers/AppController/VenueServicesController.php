<?php

namespace App\Http\Controllers\AppController;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Venue\VenueServices;
use App\Models\Venue\Venue;
use App\Models\Venue\VenueServicesReview;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Helpers\Common;
use Illuminate\Support\Facades\File;

class VenueServicesController extends Controller
{   
    private $common;

    public function __construct()
    {
        $this->common = new Common();
    } 

    public function getAllServices()
    {
        try {
            // Fetch all services with their reviews and related customers
            $services = VenueServices::with(['VenueServicesReviews.customer'])->get();
    
            // Iterate through each service to add image_url and transform reviews
            foreach ($services as $service) {
                $imagePath = 'VenueServices/' . $service->image;
                $imageUrl = asset($imagePath);
                $service->image_url = $imageUrl;
    
                // Transform the reviews to include customer names
                // $service->reviews = $service->VenueServicesReviews->map(function ($review) {
                //     return [
                //         'review_id' => $review->id,
                //         'name' => $review->customer ? $review->customer->username : 'User',
                //         'rating' => $review->rating,
                //         'review' => $review->review
                //     ];
                // });

                $services = VenueServices::with(['venueServicesReviews.customer:id,username'])->get();

// Format your response to include only the necessary data
                $formattedServices = $services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'image_url' => $service->image_url,
                        'name' => $service->name,
                        'description' => $service->description,
                        'price' => $service->price,
                        'location' => $service->location,
                        'created_at' => $service->created_at,
                        'updated_at' => $service->updated_at,
                        'reviews' => $service->venueServicesReviews->map(function ($review) {
                            return [
                                'id' => $review->id,
                                'username' => $review->customer->username,
                                'photo' => $review->customer->photo,
                                'rating' => $review->rating,
                                'review' => $review->review,
                                'created_at' => $review->created_at,
                                'updated_at' => $review->updated_at,
                            ];
                        }),
                    ];
                });

                return response()->json([
                    'status' => true,
                    'services' => $formattedServices,
                ]);

                // Remove the venue_services_reviews from the service object
                //unset($service->venue_services_reviews);
            }
    
            // Return the services with reviews as a JSON response
            return response()->json(['status' => true, 'services' => $services], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch services',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    // Create a new service
    public function createService(Request $request)
    {   
        $user = $this->common->tokenValidation($request, 'organizers');
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        if (!$user) {
            // If no user is found, return a not found response
            return response()->json(['status' => false, 'message' => 'User Not Found'], 404);
        }

        $organizer = $user;

        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'required|image|max:2048',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        // Store the image
        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension();
        $path= 'VenueServices';
        $endName= 'service';
        $imageDirectory = public_path($path);
        // dd($imageDirectory);
        if (!File::isDirectory($imageDirectory)) {
            File::makeDirectory($imageDirectory, 0777, true, true);
        }
        $imageName = $endName . uniqid() . '.' . $extension;
        $image->move($imageDirectory,$imageName);
        $imageDirectory = $path;
        $imagePath =  $imageDirectory . '/' . $imageName;
        $imageUrl = asset($imagePath);

        // Create the service
        $service = VenueServices::create([
            'organizer_id' => $organizer->id,
            'name' => $request->name,
            'image' => $imageName,
            'description' => $request->description,
            'price' => $request->price,
            'location' => $request->location,
        ]);

        $service->image_url = $imageUrl;

        return response()->json(['status' => true, 'message' => 'Service created successfully', 'service' => $service], 201);
    }


    // Update an existing service
    public function updateService(Request $request, $id)
    {
        // Find the service
        $service = VenueServices::find($id);

        if (!$service) {
            return response()->json(['status' => false, 'message' => 'Service not found'], 404);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        // Update the image if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $path = 'VenueServices';
            $endName = 'service';
            $imageDirectory = public_path($path);

            if (!File::isDirectory($imageDirectory)) {
                File::makeDirectory($imageDirectory, 0777, true, true);
            }

            $imageName = $endName . uniqid() . '.' . $extension;
            $image->move($imageDirectory, $imageName);
            $imagePath = $path . '/' . $imageName;
            $imageUrl = asset($imagePath);

            // Update the image field
            $service->image = $imagePath;
        }

        $service->name = $request->name ?? $service->name;
        $service->description = $request->description ?? $service->description;
        $service->price = $request->price ?? $service->price;
        $service->location = $request->location ?? $service->location;

        // Save the updated service
        $service->save();
        $service->image_url = $imageUrl;

        return response()->json(['status' => true, 'message' => 'Service updated successfully', 'service' => $service], 200);
    }


    // Delete a service
    public function deleteService($id)
    {
        // Find the service
        $service = VenueServices::find($id);

        if (!$service) {
            return response()->json(['status' => false, 'message' => 'Service not found'], 404);
        }

        // Delete the service
        $service->delete();

        return response()->json(['status' => true, 'message' => 'Service deleted successfully'], 200);
    }

    public function getServices(Request $request)
    {   
        $user = $this->common->tokenValidation($request, 'organizers');
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        if (!$user) {
            // If no user is found, return a not found response
            return response()->json(['status' => false, 'message' => 'User Not Found'], 404);
        }

        $organizer = $user;

        $services = VenueServices::where('organizer_id', $organizer->id)->get();
         // Iterate through each service to add image_url
        foreach ($services as $service) {
        $imagePath = 'VenueServices' . '/' . $service->image;
        $imageUrl = asset($imagePath);
        $service->image_url = $imageUrl;
    }
        return response()->json(['status' => true, 'services' => $services], 200);
    }

    public function createreview(Request $request)
    {
        $user = $this->common->tokenValidation($request, 'customers');
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        if (!$user) {
            // If no user is found, return a not found response
            return response()->json(['status' => false, 'message' => 'User Not Found'], 404);
        }

        $customer = $user;
        // Define the validation rules according to the Venue model
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:venue_services,id',
            'rating' => 'required|integer|between:1,5',
            'review' => 'required|string|max:100',
        ]);
        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $review = new VenueServicesReview();
        $review->service_id = $request->service_id;
        $review->customer_id = $customer->id; // Getting the customer ID from the authenticated user
        $review->rating = $request->rating;
        $review->review = $request->review;
        $review->save();

        return response()->json(['message' => 'Review added successfully', 'review' => $review], 201);
    }

    public function getReviews($service_id)
    {
        try {
            $reviews = VenueServicesReview::where('service_id', $service_id)
                ->with('customer:id,username') // Ensure 'customer' relationship is correctly defined
                ->get();

            $transformedReviews = $reviews->map(function ($review) {
                return [
                    'review_id' => $review->id,
                    'name' => $review->customer ? $review->customer->username : 'User',
                    'rating' => $review->rating,
                    'review' => $review->review
                ];
            });

            return response()->json([
                'status' => true,
                'reviews' => $transformedReviews
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getVenueServiceDetails($id)
    {
        try {
            // Fetch the venue service details with related reviews
            $venueService = VenueServices::with(['VenueServicesReviews.customer']) // Eager load related models
                ->findOrFail($id);
    
            // Transform reviews to include customer names
            $reviews = $venueService->VenueServicesReviews ? $venueService->VenueServicesReviews->map(function ($review) {
                return [
                    'review_id' => $review->id,
                    'name' => $review->customer ? $review->customer->username : 'User',
                    'rating' => $review->rating,
                    'review' => $review->review
                ];
            }) : [];
    
            // Handle images directly (since image is a string)
            $images = $venueService->image ? [
                [
                    'image_id' => $venueService->id,
                    'image_url' => asset('VenueServices/' . $venueService->image)
                ]
            ] : [];
    
            // Prepare response data
            $response = [
                'status' => true,
                'venue_service' => [
                    'id' => $venueService->id,
                    'name' => $venueService->name,
                    'description' => $venueService->description,
                    'price' => $venueService->price,
                    'location' => $venueService->location,
                    'images' => $images,
                ],
                'reviews' => $reviews
            ];
    
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch venue service details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    

}
