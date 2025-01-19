<?php

namespace App\Http\Controllers\AppController;

use App\Models\Venue\VenueCoupon;
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

class CouponController extends Controller
{   
    private $common;

    public function __construct()
    {
        $this->common = new Common();
    }

    public function store(Request $request)
    {   
        $user = $this->common->tokenValidation($request, 'organizers');
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
        }


        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'discount_percentage' => 'required|integer|min:1|max:100',
            'max_usage' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $coupon = VenueCoupon::create($request->all());

        return response()->json([
            'success' => true,
            'coupon' => $coupon,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found.',
            ], 404);
        }

        $request->validate([
            'code' => 'nullable|string|unique:coupons,code,' . $id,
            'discount_percentage' => 'nullable|integer|min:1|max:100',
            'max_usage' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $coupon->update($request->all());

        return response()->json([
            'success' => true,
            'coupon' => $coupon,
        ], 200);
    }

    public function list(Request $request)
    {
        $coupon = VenueCoupon::all();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'coupon' => $coupon,
        ], 200);
    }

    public function destroy($id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found.',
            ], 404);
        }

        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully.',
        ], 200);
    }
}
