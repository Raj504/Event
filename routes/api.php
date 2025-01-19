<?php

use App\Http\Controllers\AppController\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController\OrganizerController;
use App\Http\Controllers\AppController\VenueController;
use App\Http\Controllers\AppController\HomeController;
use App\Http\Controllers\AppController\VenueServicesController;
use App\Http\Controllers\AppController\VenueBookingController;
use App\Http\Controllers\AppController\VenueServicesBookingController;
use App\Http\Controllers\AppController\CouponController;
use App\Http\Controllers\AppController\InstallmentController;
//Mobile Application Routes

Route::post('optimize-clear', 'App\Http\Controllers\OptimizeClearController@optimizeClear');
Route::post('checkemail', [OrganizerController::class, 'checkemail']);


// Organizer Routes
Route::post('organizersignup', [OrganizerController::class, 'signUp']);
Route::post('organizerlogin', [OrganizerController::class, 'login']);
Route::post('updateOrganizer', [OrganizerController::class, 'update']);
Route::get('getOrganizer', [OrganizerController::class, 'getOrganizer']);
Route::get('getVenueBookingsForOrganizer', [OrganizerController::class, 'getVenueBookingsForOrganizer']);
Route::get('getServiceBookingsForOrganizer', [OrganizerController::class, 'getServiceBookingsForOrganizer']);
Route::post('confirm-booking', [OrganizerController::class, 'confirmBooking']);
Route::post('confirm-booking-service', [OrganizerController::class, 'confirmBookingService']);
Route::post('cancel-booking', [OrganizerController::class, 'cancelBooking']);
Route::post('cancel-booking-service', [OrganizerController::class, 'cancelBookingService']);
Route::post('togglevenuestatus', [OrganizerController::class, 'toggleStatus']);
Route::post('service-status', [OrganizerController::class, 'toggleServiceStatus']);

//Customer Routes
Route::post('customersignup', [CustomerController::class, 'signUp']);
Route::post('customerlogin', [CustomerController::class, 'login']);
Route::post('customerupdate', [CustomerController::class, 'update']);
Route::get('getCustomer', [CustomerController::class, 'getCustomer']);
Route::get('get-bookings', [CustomerController::class, 'getBookingsForCustomer']);
Route::post('cancel-bookingbycustomer', [CustomerController::class, 'cancelBooking']);
Route::get('cancel-bookingbycustomer', [CustomerController::class, 'cancelBooking']);
Route::get('get-invoice/{bookingId}', [CustomerController::class, 'generateInvoice']);
Route::get('get-invoice-service/{bookingId}', [CustomerController::class, 'generateInvoiceForService']);




//Home Routes
Route::post('banners', [HomeController::class, 'storeBanner']);
Route::get('banners', [HomeController::class, 'banner']);
Route::get('home/{location}', [HomeController::class, 'index']);
Route::get('search/{location}', [HomeController::class, 'getVenuesAndServicesByLocation']);

//Venue Routes
Route::get('categories', [VenueController::class, 'getCategories']);
Route::get('type', [VenueController::class, 'getType']);
Route::post('createvenue', [VenueController::class, 'store']);
Route::post('venueimage', [VenueController::class, 'storeImage']);
Route::post('add-categories', [VenueController::class, 'storeVenueCategory']);
Route::post('add-review', [VenueController::class, 'createreview']);
Route::get('venue/{id}', [VenueController::class, 'getVenueDetails']);
Route::get('getvenues', [VenueController::class, 'getVenues']);
Route::post('venues-by-category', [VenueController::class, 'getVenuesByCategory']);
Route::get('reviews/{venue_id}', [VenueController::class, 'getReviews']);
Route::get('venues-with-price/{location}', [VenueController::class, 'getVenuesWithPrice']);
Route::post('venue-available-slots', [VenueBookingController::class, 'getAvailableSlots']);
Route::post('book-venue', [VenueBookingController::class, 'createBooking']);



//Services Routes
Route::post('createService', [VenueServicesController::class, 'createService']);
Route::post('updateService/{id}', [VenueServicesController::class, 'updateService']);
Route::delete('deleteService/{id}', [VenueServicesController::class, 'deleteService']);
Route::get('getservices', [VenueServicesController::class, 'getServices']);
Route::get('services', [VenueServicesController::class, 'getAllServices']);
Route::post('add-review-service', [VenueServicesController::class, 'createreview']);
Route::get('service/{id}', [VenueServicesController::class, 'getVenueServiceDetails']);
Route::post('service-available-slots', [VenueServicesBookingController::class, 'getAvailableSlots']);
Route::post('book-service', [VenueServicesBookingController::class, 'createBooking']);
Route::get('get-service-bookings', [VenueServicesBookingController::class, 'getBookingsForCustomer']);

//coupon
Route::post('create-coupon', [CouponController::class, 'store']);
Route::get('coupon', [CouponController::class, 'list']);
Route::put('update-coupons/{id}', [CouponController::class, 'update']);
Route::delete('delete-coupon/{id}', [CouponController::class, 'destroy']);



Route::post('installments/create', [InstallmentController::class, 'createInstallments']);
Route::post('installments/pay', [InstallmentController::class, 'updateInstallmentStatus']);
Route::get('get-installments/{bookingId}', [InstallmentController::class, 'getInstallments']);
Route::post('service-installments/create', [InstallmentController::class, 'createServiceInstallments']);
Route::post('service-installments/pay', [InstallmentController::class, 'updateServiceInstallmentStatus']);
Route::get('service-get-installments/{bookingId}', [InstallmentController::class, 'getServiceInstallments']);







