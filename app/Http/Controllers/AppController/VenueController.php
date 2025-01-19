<?php

namespace App\Http\Controllers\AppController;

use App\Http\Controllers\Controller;
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
use App\Models\Venue\VenueType;
use App\Models\Venue\VenueImages;
use App\Models\Venue\VenueReview;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class VenueController extends Controller
{   private $common;

    public function __construct()
    {
        $this->common = new Common();
    }  
    
    public function getVenuesWithPrice($location)
    {   
        try {
            // Fetch venues where price is not null
            $topVenues = Venue::select(
                'venues.id', 
                'venues.name', 
                'venues.description', 
                'venues.price', 
                'venues.veg_price',
                'venues.non_veg_price',
                'venues.capacity', 
                'venues.total_rooms', 
                'venues.parking', 
                'venues.ac',
                'venues.wifi',
                'venues.decoration',
                'venues.bar',
                'venues.location', 
                'venues.cancellation_policy', \DB::raw('CAST(AVG(venue_reviews.rating) AS UNSIGNED) as average_rating'))
            ->leftJoin('venue_reviews', 'venues.id', '=', 'venue_reviews.venue_id')
            ->whereNotNull('venues.price')
            ->whereNull('venues.veg_price')
            ->whereNull('venues.non_veg_price')
            ->where('venues.location', 'LIKE', '%' . $location . '%')
            ->groupBy(
                'venues.id', 
                'venues.name', 
                'venues.description', 
                'venues.price', 
                'venues.veg_price',
                'venues.non_veg_price',
                'venues.capacity', 
                'venues.total_rooms', 
                'venues.parking', 
                'venues.ac',
                'venues.wifi',
                'venues.decoration',
                'venues.bar',
                'venues.location', 
                'venues.cancellation_policy')
            ->orderBy('average_rating', 'desc')
            ->limit(5)
            ->get();

        foreach ($topVenues as $venue) {
            $venue->images = VenueImages::where('venue_id', $venue->id)->pluck('image_url');
        }

        // Fetch popular venues with images and where price is not null
        $popularVenues = Venue::select(
            'venues.id', 
            'venues.name', 
            'venues.description', 
            'venues.price', 
            'venues.veg_price',
            'venues.non_veg_price',
            'venues.capacity', 
            'venues.total_rooms', 
            'venues.parking', 
            'venues.ac',
            'venues.wifi',
            'venues.decoration',
            'venues.bar',
            'venues.location', 
            'venues.cancellation_policy', \DB::raw('CAST(AVG(venue_reviews.rating) AS UNSIGNED) as average_rating'))
            ->leftJoin('venue_reviews', 'venues.id', '=', 'venue_reviews.venue_id')
            ->whereNotNull('venues.price')
            ->whereNull('venues.veg_price')
            ->whereNull('venues.non_veg_price')
            ->where('venues.location', 'LIKE', '%' . $location . '%')
            ->groupBy(
                'venues.id', 
                'venues.name', 
                'venues.description', 
                'venues.price', 
                'venues.veg_price',
                'venues.non_veg_price',
                'venues.capacity', 
                'venues.total_rooms', 
                'venues.parking', 
                'venues.ac',
                'venues.wifi',
                'venues.decoration',
                'venues.bar',
                'venues.location', 
                'venues.cancellation_policy')
            ->orderBy('average_rating', 'desc')
            ->limit(5)
            ->get();

        foreach ($popularVenues as $venue) {
            $venue->images = VenueImages::where('venue_id', $venue->id)->pluck('image_url');
        }

        $fixedprice = [
            'topVenues' => $topVenues,
            'popularVenues' => $popularVenues
        ];

            return response()->json([
                'success' => true,
                'venues' => $fixedprice
            ], 200);
        } catch (Exception $e) {
            // Log the exception for internal tracking
            \Log::error('Fetching venues failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching venues.',
                'error' => $e->getMessage() 
            ], 500);
        }
    }

    public function getCategories()
    {
        try {
            $categories = VenueCategory::all(); // Fetch all categories

            // Manually add image URL to each category
            foreach ($categories as $category) {
                $category->image_url = $category->image;
            }

            return response()->json([
                'status' => true,
                'categories' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getType()
    {
        try {
            $categories = VenueType::all();
            return response()->json([
                'status' => true,
                'categories' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        // Validate the token and get the authenticated user
        $user = $this->common->tokenValidation($request, 'organizers');
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        if (!$user) {
            // If no user is found, return a not found response
            return response()->json(['status' => false, 'message' => 'User Not Found'], 404);
        }

        $organizer = $user;

        // Define the validation rules according to the Venue model
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'veg_price' => 'nullable|numeric',
            'non_veg_price' => 'nullable|numeric',
            'capacity' => 'required|integer',
            'total_rooms' => 'required|integer',
            'parking' => 'required|boolean',
            'ac' => 'required|boolean',
            'wifi' => 'required|boolean',
            'decoration' => 'required|boolean',
            'bar' => 'required|boolean',
            'location' => 'required|string|max:255',
            'cancellation_policy' => 'nullable|string',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        // Create the venue
        $venue = Venue::create([
            'organizer_id' => $organizer->id,
            'name' => $request->name,
            'type' => $request->type,
            'category' => $request->category,
            'description' => $request->description,
            'price' => $request->price,
            'veg_price' => $request->veg_price,
            'non_veg_price' => $request->non_veg_price,
            'capacity' => $request->capacity,
            'total_rooms' => $request->total_rooms,
            'parking' => $request->parking,
            'ac'=> $request->ac,
            'wifi' => $request->wifi,
            'decoration' => $request->decoration,
            'bar' => $request->bar,
            'location' => $request->location,
            'cancellation_policy' => $request->cancellation_policy,
            // Ensure that all fields in the create method are present in the model
        ]);

        return response()->json(['status' => true, 'venue' => $venue], 201);
    }

    public function storeImage(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'venue_id' => 'required|exists:venues,id',
            'images' => 'required|array|max:8', // Ensure it's an array with a maximum of 8 images
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120', // Validate each image
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }
    
        // Array to store image records
        $venueImages = [];
    
        // Handle multiple file uploads
        foreach ($request->file('images') as $image) {
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension(); // Generate a unique name for each image
            $destinationPath = public_path('venue_images'); // Define the directory
            $image->move($destinationPath, $imageName); // Move the image to the public directory
    
            $imageUrl = asset('venue_images/' . $imageName); // Generate the URL
    
            // Create a new VenueImage record
            $venueImage = VenueImages::create([
                'venue_id' => $request->venue_id,
                'image' => $imageName,
                'image_url' => $imageUrl,
            ]);
    
            // Add the created image record to the array
            $venueImages[] = $venueImage;
        }
    
        return response()->json(['status' => true, 'venue_images' => $venueImages], 201);
    }

    public function update(Request $request, $id)
    {
        // Validate the token and get the authenticated user
        $user = $this->common->tokenValidation($request, 'organizers');
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User Not Found'], 404);
        }

        $organizer = $user;

        // Find the venue by ID and ensure it belongs to the authenticated organizer
        $venue = Venue::where('id', $id)->where('organizer_id', $organizer->id)->first();
        if (!$venue) {
            return response()->json(['status' => false, 'message' => 'Venue Not Found or Unauthorized'], 404);
        }

        // Define the validation rules according to the Venue model
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'veg_price' => 'nullable|numeric',
            'non_veg_price' => 'nullable|numeric',
            'capacity' => 'required|integer',
            'total_rooms' => 'required|integer',
            'parking' => 'required|boolean',
            'ac' => 'required|boolean',
            'wifi' => 'required|boolean',
            'decoration' => 'required|boolean',
            'bar' => 'required|boolean',
            'location' => 'required|string|max:255',
            'cancellation_policy' => 'nullable|string',
            // Add any other fields that you may need
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        // Update the venue with validated data
        $venue->update($validator->validated());

        return response()->json(['status' => true, 'venue' => $venue], 200);
    }

    public function destroy($id)
    {
        $venue = Venue::findOrFail($id);
        $venue->delete();
        return response()->json(['message' => 'Venue deleted successfully'], Response::HTTP_OK);
    }

    public function storeVenueCategory(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }

        try {
            // Handle the image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $extension = $image->getClientOriginalExtension();
                $path = 'Venuecategories';
                $endName = 'category';
                $imageDirectory = public_path($path);

                if (!File::isDirectory($imageDirectory)) {
                    File::makeDirectory($imageDirectory, 0777, true, true);
                }

                $imageName = $endName . uniqid() . '.' . $extension;
                $image->move($imageDirectory, $imageName);  
                $imageUrl = $path . '/' . $imageName;
            } else {
                $imageUrl = null; // If no image is provided
                $imageName = null; // If no image is provided
            }

            // Create a new venue category
            $category = new VenueCategory();
            $category->name = $request->input('name');
            $category->image = $imageName; // Save only the image name
            $category->save();

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Venue category created successfully',
                'data' => $category
            ], 201);
        } catch (Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
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
            'venue_id' => 'required|exists:venues,id',
            'rating' => 'required|integer|between:1,5',
            'review' => 'required|string|max:100',
        ]);
        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $review = new VenueReview();

        $review->venue_id = $request->venue_id;
        $review->customer_id = $customer->id; // Getting the customer ID from the authenticated user
        $review->rating = $request->rating;
        $review->review = $request->review;
        $review->save();

        return response()->json(['message' => 'Review added successfully', 'review' => $review], 201);
    }

    public function getReviews($venue_id)
    {
        try {
            $reviews = VenueReview::where('venue_id', $venue_id)
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

    public function getVenueDetails($id)
    {
        try {
            // Fetch the venue details with related type and category
            $venue = Venue::with(['venueImages', 'venueReviews.customer', 'venueType', 'venueCategory']) // Eager load related models
                ->findOrFail($id);

            // Transform reviews to include customer names
            $reviews = $venue->venueReviews->map(function ($review) {
                return [
                    'review_id' => $review->id,
                    'name' => $review->customer ? $review->customer->username : 'User',
                    'rating' => $review->rating,
                    'review' => $review->review
                ];
            });

            // Prepare response data
            $response = [
                'status' => true,
                'venue' => [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'type' => $venue->venueType ? $venue->venueType->type : null, // Fetch type name
                    'category' => $venue->venueCategory ? $venue->venueCategory->name : null, // Fetch category name
                    'description' => $venue->description,
                    'price'=>$venue->price,
                    'veg_price' => $venue->veg_price,
                    'non_veg_price' => $venue->non_veg_price,
                    'capacity' => $venue->capacity,
                    'total_rooms' => $venue->total_rooms,
                    'parking' => $venue->parking,
                    'ac'=>$venue->ac,
                    'wifi'=> $venue->wifi,
                    'decoration'=> $venue->decoration,
                    'bar'=> $venue->bar,
                    'location' => $venue->location,
                    'cancellation_policy' => $venue->cancellation_policy,
                    'images' => $venue->venueImages->map(function ($image) {
                        return [
                            'image_id' => $image->id,
                            'image_url' => $image->image_url
                        ];
                    }),
                ],
                'reviews' => $reviews
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch venue details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getVenues(Request $request)
    {
        try {
            // Validate token and get the organizer
            $user = $this->common->tokenValidation($request, 'organizers');
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
            }

            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User Not Found'], 404);
            }

            $organizer = $user;

            // Fetch all venues associated with the organizer
            $venues = Venue::with(['venueImages', 'venueReviews.customer', 'venueType', 'venueCategory'])
                ->where('organizer_id', $organizer->id)
                ->get();

            // Transform venues and reviews
            $venuesData = $venues->map(function ($venue) {
                $reviews = $venue->venueReviews->map(function ($review) {
                    return [
                        'review_id' => $review->id,
                        'name' => $review->customer ? $review->customer->username : 'User',
                        'rating' => $review->rating,
                        'review' => $review->review
                    ];
                });

                return [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'type' => $venue->venueType ? $venue->venueType->type : null,
                    'category' => $venue->venueCategory ? $venue->venueCategory->name : null,
                    'description' => $venue->description,
                    'price'=>$venue->price,
                    'veg_price' => $venue->veg_price,
                    'non_veg_price' => $venue->non_veg_price,
                    'capacity' => $venue->capacity,
                    'total_rooms' => $venue->total_rooms,
                    'parking' => $venue->parking,
                    'ac'=>$venue->ac,
                    'wifi'=> $venue->wifi,
                    'decoration'=> $venue->decoration,
                    'bar'=> $venue->bar,
                    'location' => $venue->location,
                    'cancellation_policy' => $venue->cancellation_policy,
                    'images' => $venue->venueImages->map(function ($image) {
                        return [
                            'image_id' => $image->id,
                            'image_url' => $image->image_url
                        ];
                    }),
                    'reviews' => $reviews
                ];
            });

            return response()->json(['status' => true, 'venues' => $venuesData], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch venues',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // public function getVenuesByCategory(Request $request)
    // {
    //     // Define validation rules
    //     $validator = Validator::make($request->all(), [
    //         'location' => 'required|string|max:255',
    //         'category' => 'required|exists:venue_categories,id',
    //     ]);
    
    //     // Check if validation fails
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'false',
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }
    
    //     // Get the location and category parameters from the request
    //     $location = $request->location;
    //     $category = $request->category;
    
    //     // Fetch venues with reviews and related customer data
    //     $venues = Venue::with([
    //         'venueImages',
    //         'venueReviews' => function ($query) {
    //             $query->select('id', 'venue_id', 'rating', 'review', 'customer_id', 'created_at')
    //                 ->with(['customer:id,fname,lname']);
    //         },
    //         'venueType',
    //         'venueCategory',
    //         'organizer'
    //     ])
    //     ->when($location, function ($query, $location) {
    //         return $query->where('location', 'LIKE', "%$location%");
    //     })
    //     ->when($category, function ($query, $category) {
    //         return $query->where('category', $category);
    //     })
    //     ->get();
    
    //     // Format the response
    //     $formattedVenues = $venues->map(function ($venue) {
    //         return [
    //             'id' => $venue->id,
    //             'organizer_id' => $venue->organizer_id,
    //             'organizer_name' => $venue->organizer ? $venue->organizer->username : 'Unknown',
    //             'type' => $venue->type,
    //             'category' => $venue->category,
    //             'name' => $venue->name,
    //             'description' => $venue->description,
    //             'price' => $venue->price,
    //             'veg_price' => $venue->veg_price,
    //             'non_veg_price' => $venue->non_veg_price,
    //             'capacity' => $venue->capacity,
    //             'total_rooms' => $venue->total_rooms,
    //             'parking' => $venue->parking,
    //             'ac' => $venue->ac,
    //             'wifi' => $venue->wifi,
    //             'decoration' => $venue->decoration,
    //             'bar' => $venue->bar,
    //             'location' => $venue->location,
    //             'cancellation_policy' => $venue->cancellation_policy,
    //             'venue_images' => $venue->venueImages->map(function ($image) {
    //                 return [
    //                     'id' => $image->id,
    //                     'venue_id' => $image->venue_id,
    //                     'image' => $image->image,
    //                     'image_url' => $image->image_url
    //                 ];
    //             }),
    //             'venue_reviews' => $venue->venueReviews->map(function ($review) {
    //                 return [
    //                     'id' => $review->id,
    //                     'rating' => $review->rating,
    //                     'review' => $review->review,
    //                     'created_at' => $review->created_at,
    //                     'customer_name' => $review->customer->fname . ' ' . $review->customer->lname, // Include only name
    //                 ];
    //             }),
    //             'venue_type' => [
    //                 'id' => $venue->venueType->id,
    //                 'type' => $venue->venueType->type
    //             ],
    //             'venue_category' => [
    //                 'id' => $venue->venueCategory->id,
    //                 'name' => $venue->venueCategory->name,
    //             ]
    //         ];
    //     });
    
    //     return response()->json([
    //         'status' => 'true',
    //         'venues' => $formattedVenues
    //     ]);
    // }
    public function getVenuesByCategory(Request $request)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'location' => 'required|string|max:255',
                'category' => 'required|exists:venue_categories,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $location = $validatedData['location'];
            $category = $validatedData['category'];
            $startDate = Carbon::parse($validatedData['start_date']);
            $endDate = Carbon::parse($validatedData['end_date']);

            // Fetch venues with filters
            $venues = Venue::with([
                'venueImages',
                'venueReviews' => function ($query) {
                    $query->select('id', 'venue_id', 'rating', 'review', 'customer_id', 'created_at')
                        ->with(['customer:id,username']);
                },
                'venueType',
                'venueCategory',
                'organizer'
            ])
            ->where('location', 'LIKE', "%$location%")
            ->where('category', $category)
            ->whereDoesntHave('bookings', function ($query) use ($startDate, $endDate) {
                $query->whereIn('status', ['confirmed'])
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->where('start_time', '<', $endDate)
                            ->where('end_time', '>', $startDate);
                    });
            })
            ->get();

            // Format the response
            $formattedVenues = $venues->map(function ($venue) {
                return [
                    'id' => $venue->id,
                    'organizer_id' => $venue->organizer_id,
                    'organizer_name' => $venue->organizer ? $venue->organizer->username : 'Unknown',
                    'type' => $venue->venueType->type,
                    'category' => $venue->venueCategory->name,
                    'name' => $venue->name,
                    'description' => $venue->description,
                    'price' => $venue->price,
                    'veg_price' => $venue->veg_price,
                    'non_veg_price' => $venue->non_veg_price,
                    'capacity' => $venue->capacity,
                    'total_rooms' => $venue->total_rooms,
                    'parking' => $venue->parking,
                    'ac' => $venue->ac,
                    'wifi' => $venue->wifi,
                    'decoration' => $venue->decoration,
                    'bar' => $venue->bar,
                    'location' => $venue->location,
                    'cancellation_policy' => $venue->cancellation_policy,
                    'venue_images' => $venue->venueImages->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'venue_id' => $image->venue_id,
                            'image' => $image->image,
                            'image_url' => $image->image_url
                        ];
                    }),
                    'venue_reviews' => $venue->venueReviews->map(function ($review) {
                        return [
                            'id' => $review->id,
                            'rating' => $review->rating,
                            'review' => $review->review,
                            'created_at' => $review->created_at,
                            'customer_name' => $review->customer 
                            ? $review->customer->username 
                            : 'Anonymous',
                        ];
                    }),
                ];
            });

            return response()->json([
                'status' => true,
                'venues' => $formattedVenues
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching venues',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
