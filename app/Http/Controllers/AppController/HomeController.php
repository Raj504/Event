<?php

namespace App\Http\Controllers\AppController;

use App\Http\Controllers\Controller; 
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
use App\Models\Venue\VenueType;
use App\Models\Venue\VenueReview;
use App\Models\Venue\VenueCategory;
use App\Models\Venue\VenueImages;
use App\Models\Venue\VenueServices;
use App\Models\Venue\VenueCoupon;
use App\Http\Controllers\AppController\VenueController;
use Carbon\Carbon;


class HomeController extends Controller
{   private $common;

    public function __construct()
    {
        $this->common = new Common();
    }
    public function storeBanner(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->move('public/banners', $imageName, 'public');

                // Create a new banner
                $banner = new Banners();
                $banner->image = $imageName;
                $banner->image_url = asset($imagePath);
                $banner->save();

                // Return a success response
                return response()->json([
                    'status' => 'success',
                    'message' => 'Banner created successfully',
                    'data' => $banner
                ], 200);
            }

            // If no file was uploaded, return an error
            return response()->json([
                'status' => 'error',
                'message' => 'No image file uploaded'
            ], 422);

        } catch (Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function banner(Request $request)
    {
        // Fetch banners and coupons
        $banners = Banners::all();
        $coupon = VenueCoupon::all();

        // Prepare the response
        $response = [
            'status' => true,
            'banners' => $banners,
            'coupon' => $coupon,
        ];

        // Add additional messages if any data is missing
        if ($banners->isEmpty()) {
            $response['banners_message'] = 'No banners found.';
        }

        if ($coupon->isEmpty()) {
            $response['coupon_message'] = 'No coupons found.';
        }

        // Return the response with a 200 status, including both banners and coupons (even if one is empty)
        return response()->json($response, 200);
    }

    // public function index($location)
    // {
    //     try {
    //         // Retrieve all banners
    //         $banners = Banners::all();
    
    //         // Fetch all categories with image URLs
    //         $categories = VenueCategory::all();
    //         foreach ($categories as $category) {
    //             $category->image_url = $category->image ? $this->common->getImageUrl($category->image, 'Venuecategories') : null;
    //         }
    
    //         // Fetch top venues with images based on location where status is active (status = 1)
    //         $topVenues = Venue::select(
    //             'venues.id', 
    //             'venues.name', 
    //             'venues.description', 
    //             'venues.price', 
    //             'venues.veg_price',
    //             'venues.non_veg_price',
    //             'venues.capacity', 
    //             'venues.total_rooms', 
    //             'venues.parking', 
    //             'venues.ac',
    //             'venues.wifi',
    //             'venues.decoration',
    //             'venues.bar',
    //             'venues.location',
    //             'venues.cancellation_policy', 
    //             \DB::raw('CAST(AVG(venue_reviews.rating) AS UNSIGNED) as average_rating')
    //         )
    //         ->where('venues.location', 'like', '%' . $location . '%')
    //         ->where('venues.status', 1) // Check for active status
    //         ->whereNotNull('venues.price')
    //         ->whereNotNull('venues.veg_price')
    //         ->whereNotNull('venues.non_veg_price')
    //         ->leftJoin('venue_reviews', 'venues.id', '=', 'venue_reviews.venue_id')
    //         ->groupBy(
    //             'venues.id', 
    //             'venues.name', 
    //             'venues.description', 
    //             'venues.price', 
    //             'venues.veg_price',
    //             'venues.non_veg_price',
    //             'venues.capacity', 
    //             'venues.total_rooms', 
    //             'venues.parking', 
    //             'venues.ac',
    //             'venues.wifi',
    //             'venues.decoration',
    //             'venues.bar',
    //             'venues.location',
    //             'venues.cancellation_policy'
    //         )
    //         ->orderBy('average_rating', 'desc')
    //         ->limit(5)
    //         ->get();
    
    //         foreach ($topVenues as $venue) {
    //             $venue->images = VenueImages::where('venue_id', $venue->id)->pluck('image_url');
    //         }
    
    //         // Fetch popular venues with images based on location where status is active (status = 1)
    //         $popularVenues = Venue::select(
    //             'venues.id', 
    //             'venues.name', 
    //             'venues.description', 
    //             'venues.price', 
    //             'venues.veg_price',
    //             'venues.non_veg_price',
    //             'venues.capacity', 
    //             'venues.total_rooms', 
    //             'venues.parking', 
    //             'venues.ac',
    //             'venues.wifi',
    //             'venues.decoration',
    //             'venues.bar',
    //             'venues.location',                
    //             'venues.cancellation_policy', 
    //             \DB::raw('CAST(AVG(venue_reviews.rating) AS UNSIGNED) as average_rating')
    //         )
    //         ->where('venues.location', 'like', '%' . $location . '%')
    //         ->where('venues.status', 1) // Check for active status
    //         ->whereNotNull('venues.price')
    //         ->whereNotNull('venues.veg_price')
    //         ->whereNotNull('venues.non_veg_price')
    //         ->leftJoin('venue_reviews', 'venues.id', '=', 'venue_reviews.venue_id')
    //         ->groupBy(
    //             'venues.id', 
    //             'venues.name', 
    //             'venues.description', 
    //             'venues.price', 
    //             'venues.veg_price',
    //             'venues.non_veg_price',
    //             'venues.capacity', 
    //             'venues.total_rooms', 
    //             'venues.parking', 
    //             'venues.ac',
    //             'venues.wifi',
    //             'venues.decoration',
    //             'venues.bar',
    //             'venues.location',
    //             'venues.cancellation_policy'
    //         )
    //         ->orderBy('average_rating', 'desc')
    //         ->limit(5)
    //         ->get();
    
    //         foreach ($popularVenues as $venue) {
    //             $venue->images = VenueImages::where('venue_id', $venue->id)->pluck('image_url');
    //         }
    
    //         // Prepare the response data
    //         $data = [
    //             'banners' => $banners,
    //             'venueCategories' => $categories,
    //             'topVenues' => $topVenues,
    //             'popularVenues' => $popularVenues
    //         ];
    
    //         // Return the data in a JSON response
    //         return response()->json([
    //             'status' => 'true',
    //             'home' => $data
    //         ], 200);
    
    //     } catch (\Exception $e) {
    //         // Return an error response if something goes wrong
    //         return response()->json([
    //             'status' => 'false',
    //             'message' => 'Failed to fetch venues',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function index(Request $request, $location)
    {
        try {
            // Validate optional start_date, end_date, guest_capacity, veg, non_veg
            $validatedData = $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'guest_capacity' => 'nullable|integer|min:1',
                'veg' => 'nullable|boolean', // Filter for venues with veg_price
                'non_veg' => 'nullable|boolean', // Filter for venues with non_veg_price
            ]);
    
            // Parse the start and end date
            $startDate = isset($validatedData['start_date']) ? Carbon::parse($validatedData['start_date']) : null;
            $endDate = isset($validatedData['end_date']) ? Carbon::parse($validatedData['end_date']) : null;
    
            // Retrieve all banners
            $banners = Banners::all();
    
            // Fetch all categories with image URLs
            $categories = VenueCategory::all();
            foreach ($categories as $category) {
                $category->image_url = $category->image
                    ? $this->common->getImageUrl($category->image, 'Venuecategories')
                    : null;
            }
    
            // Query base for top and popular venues
            $baseQuery = Venue::select(
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
                'venues.cancellation_policy',
                \DB::raw('CAST(AVG(venue_reviews.rating) AS UNSIGNED) as average_rating')
            )
            ->where('venues.location', 'like', '%' . $location . '%')
            ->where('venues.status', 1) // Active venues only
            ->whereNotNull('venues.price')
            ->leftJoin('venue_reviews', 'venues.id', '=', 'venue_reviews.venue_id')
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
                'venues.cancellation_policy'
            );
    
            // Apply filters for guest_capacity, veg, and non_veg availability
            if (isset($validatedData['guest_capacity'])) {
                $baseQuery->where('venues.capacity', '>=', $validatedData['guest_capacity']);
            }
            if (isset($validatedData['veg']) && $validatedData['veg'] == 1) {
                $baseQuery->where('venues.veg_price', '>', 0); // Ensure veg_price > 0
            } else {
                $baseQuery->where(function ($query) {
                    $query->whereNull('venues.veg_price')
                          ->orWhere('venues.veg_price', '<=', 0); // Exclude venues with veg_price <= 0
                });
            }
            
            if (isset($validatedData['non_veg']) && $validatedData['non_veg'] == 1) {
                $baseQuery->where('venues.non_veg_price', '>', 0); // Ensure non_veg_price > 0
            } else {
                $baseQuery->where(function ($query) {
                    $query->whereNull('venues.non_veg_price')
                          ->orWhere('venues.non_veg_price', '<=', 0); // Exclude venues with non_veg_price <= 0
                });
            }
            
    
            // Filter venues based on start_date and end_date if provided
            if ($startDate && $endDate) {
                $baseQuery->whereDoesntHave('bookings', function ($query) use ($startDate, $endDate) {
                    $query->whereIn('status', ['confirmed'])
                        ->where(function ($query) use ($startDate, $endDate) {
                            $query->orWhere(function ($query) use ($startDate, $endDate) {
                                    $query->where('start_date', '<=', $startDate)
                                          ->where('end_date', '>=', $endDate);
                                });
                        });
                });
            }
    
            // Fetch top venues
            $topVenues = (clone $baseQuery)
                ->orderBy('average_rating', 'desc')
                ->limit(5)
                ->get();
    
            foreach ($topVenues as $venue) {
                $venue->images = VenueImages::where('venue_id', $venue->id)->pluck('image_url');
            }
    
            // Fetch popular venues
            $popularVenues = (clone $baseQuery)
                ->orderBy('average_rating', 'desc')
                ->limit(5)
                ->get();
    
            foreach ($popularVenues as $venue) {
                $venue->images = VenueImages::where('venue_id', $venue->id)->pluck('image_url');
            }
    
            // Prepare the response data
            $data = [
                'banners' => $banners,
                'venueCategories' => $categories,
                'topVenues' => $topVenues,
                'popularVenues' => $popularVenues,
            ];
    
            // Return the data in a JSON response
            return response()->json([
                'status' => 'true',
                'home' => $data
            ], 200);
    
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'status' => 'false',
                'message' => 'Failed to fetch venues',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    


    public function getVenuesAndServicesByLocation($location)
    {
        try {
            // Fetch all venues based on location and status
            $venues = Venue::select(
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
                    'venues.cancellation_policy', 
                    \DB::raw('CAST(AVG(venue_reviews.rating) AS UNSIGNED) as average_rating')
                )
                ->where('venues.location', 'like', '%' . $location . '%')
                ->where('venues.status', 1) // Filter by status = 1
                ->leftJoin('venue_reviews', 'venues.id', '=', 'venue_reviews.venue_id')
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
                    'venues.cancellation_policy'
                )
                ->orderBy('average_rating', 'desc')
                ->get();

            foreach ($venues as $venue) {
                $venue->images = VenueImages::where('venue_id', $venue->id)->pluck('image_url');
            }

            // Fetch all services based on location and status
            $services = VenueServices::where('location', 'like', '%' . $location . '%')
                ->where('status', 1) // Filter by status = 1
                ->get();
            
            foreach ($services as $service) {
                $service->image_url = $service->image_url; // This uses the accessor method in the model
            }

            // Prepare the response data
            $data = [
                'venues' => $venues,
                'services' => $services
            ];

            // Return the data in a JSON response
            return response()->json([
                'status' => 'true',
                'data' => $data
            ], 200); // HTTP 200 OK

        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'status' => 'false',
                'message' => 'Failed to fetch venues and services',
                'error' => $e->getMessage()
            ], 500); // HTTP 500 Internal Server Error
        }
    }

}
