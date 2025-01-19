<?php

namespace App\Models;

use App\Models\Event\Booking;
use App\Models\Event\Wishlist;
use App\Models\ShopManagement\OrderItem;
use App\Models\ShopManagement\ProductOrder;
use App\Models\ShopManagement\ProductReview;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;


class Customer extends Model implements AuthenticatableContract
{
  use HasFactory, Authenticatable;

  protected $table = 'users'; 

  protected $fillable = [
    'first_name',
    'last_name',
    'username',
    'email',
    'image',
    'contact_number',
    'address',
    'country',
    'state',
    'city',
    'zip_code',
    'password',
    'gender',
    'status',
    'api_token',
    'email_verified_at',
    'verification_token'
  ];

  protected $hidden = [
    'password',
    'remember_token',
    'two_factor_recovery_codes',
    'two_factor_secret',
  ];

  protected $casts = [
    'email_verified_at' => 'datetime',
  ];
  /**
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = [
    'profile_photo_url',
  ];

  public function getProfilePhotoUrlAttribute()
  {
      return $this->image ? asset('images/customer/' . $this->image) : null;
  }


  //bookings
  public function bookings()
  {
    return $this->hasMany(Booking::class);
  }
  //order_items
  public function order_items()
  {
    return $this->hasMany(OrderItem::class, 'user_id', 'id');
  }
  //product_orders
  public function product_orders()
  {
    return $this->hasMany(ProductOrder::class, 'user_id', 'id');
  }
  //product_reviews
  public function product_reviews()
  {
    return $this->hasMany(ProductReview::class, 'user_id', 'id');
  }
  //support_tickets
  public function support_tickets()
  {
    return $this->hasMany(SupportTicket::class, 'user_id', 'id');
  }
  //wishlists
  public function wishlists()
  {
    return $this->hasMany(Wishlist::class, 'customer_id', 'id');
  }
}
