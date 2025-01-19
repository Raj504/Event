<?php

namespace App\Http\Controllers\AppController;

use App\Models\Venue\Installment;
use App\Models\Venue\VenueBooking;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Helpers\Common;
use App\Models\Venue\InstallmentService;
use App\Models\Venue\VenueServicesBooking;

class InstallmentController extends Controller
{
    private $common;

    public function __construct()
    {
        $this->common = new Common();
    }

    // Generate installments based on user choice
    public function createInstallments(Request $request)
    {
        try {
            // Validate token and retrieve user
            $user = $this->common->tokenValidation($request, 'customers');
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
            }
    
            // Validate the input data
            $validator = Validator::make($request->all(), [
                'number_of_installments' => 'required|integer|min:1',
                'booking_id' => 'required|integer|exists:venue_bookings,id',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }
    
            $booking = VenueBooking::find($request->booking_id);
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found.',
                ], 404);
            }
    
            // Check if installments already exist for this booking
            if (Installment::where('booking_id', $booking->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Installments for this booking have already been created.',
                ], 409); // 409 Conflict
            }
    
            $totalAmount = $booking->amount;
            $installmentsCount = $request->number_of_installments;
            $installmentAmount = round($totalAmount / $installmentsCount, 2);
            $lastInstallmentAmount = $installmentAmount + ($totalAmount - $installmentAmount * $installmentsCount);
    
            // Create the installments
            for ($i = 1; $i <= $installmentsCount; $i++) {
                Installment::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $user->id,
                    'amount' => $i === $installmentsCount ? $lastInstallmentAmount : $installmentAmount, // Adjust last installment
                    'status' => 'pending',
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Installments created successfully.',
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create installments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    // Fetch installments for a booking
    public function getInstallments($bookingId)
    {
        try {
            $validator = Validator::make(['booking_id' => $bookingId], [
                'booking_id' => 'required|integer|exists:venue_bookings,id',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $installments = Installment::where('booking_id', $bookingId)->get();

            return response()->json([
                'success' => true,
                'installments' => $installments,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch installments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Change installment status
    public function updateInstallmentStatus(Request $request)
    {
        try {
            // Validate the input data
            $validator = Validator::make($request->all(), [
                'installment_id' => 'required|integer|exists:installments,id',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }
    
            // Find the installment
            $installment = Installment::findOrFail($request->installment_id);
    
            // Update the installment status
            $installment->update([
                'status' => 'paid',
            ]);
    
            // Recalculate the total paid amount for the booking
            $totalPaidAmount = Installment::where('booking_id', $installment->booking_id)
                                          ->where('status', 'paid')
                                          ->sum('amount');
    
            // Update the paid amount and payment status of the related booking
            $venueBooking = VenueBooking::find($installment->booking_id);
            $venueBooking->update([
                'paid_amount' => $totalPaidAmount,
            ]);
    
            // Check if all installments for this booking are paid
            $allPaid = Installment::where('booking_id', $installment->booking_id)
                                  ->where('status', '!=', 'paid')
                                  ->doesntExist();
    
            if ($allPaid) {
                $venueBooking->update([
                    'payment' => 'paid',
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Installment status and paid amount updated successfully.',
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update installment status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function createServiceInstallments(Request $request)
    {
        try {
            // Validate token and retrieve user
            $user = $this->common->tokenValidation($request, 'customers');
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
            }

            // Validate the input data
            $validator = Validator::make($request->all(), [
                'number_of_installments' => 'required|integer|min:1',
                'booking_id' => 'required|integer|exists:venue_services_bookings,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $booking = VenueServicesBooking::find($request->booking_id);
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found.',
                ], 404);
            }

            // Check if installments already exist for this booking
            if (InstallmentService::where('booking_id', $booking->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Installments for this booking have already been created.',
                ], 409); // 409 Conflict
            }

            $totalAmount = $booking->amount;
            $installmentsCount = $request->number_of_installments;
            $installmentAmount = round($totalAmount / $installmentsCount, 2);
            $lastInstallmentAmount = $installmentAmount + ($totalAmount - $installmentAmount * $installmentsCount);

            // Create the installments
            for ($i = 1; $i <= $installmentsCount; $i++) {
                InstallmentService::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $user->id,
                    'amount' => $i === $installmentsCount ? $lastInstallmentAmount : $installmentAmount, // Adjust last installment
                    'status' => 'pending',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Installments created successfully.',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create installments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getServiceInstallments($bookingId)
    {
        try {
            $validator = Validator::make(['booking_id' => $bookingId], [
                'booking_id' => 'required|integer|exists:venue_services_bookings,id',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $installments = InstallmentService::where('booking_id', $bookingId)->get();

            return response()->json([
                'success' => true,
                'installments' => $installments,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch installments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateServiceInstallmentStatus(Request $request)
    {
        try {
            // Validate the input data
            $validator = Validator::make($request->all(), [
                'installment_id' => 'required|integer|exists:installments,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Find the installment
            $installment = InstallmentService::findOrFail($request->installment_id);

            // Update the installment status
            $installment->update([
                'status' => 'paid',
            ]);

            // Check if all installments for this booking are paid
            $allPaid = InstallmentService::where('booking_id', $installment->booking_id)
                                ->where('status', '!=', 'paid')
                                ->doesntExist();

            if ($allPaid) {
                // Update the payment status of the related booking
                $venueServicesBooking = VenueServicesBooking::find($installment->booking_id);
                $venueServicesBooking->update([
                    'payment' => 'paid',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Installment status updated successfully.',
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update installment status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
