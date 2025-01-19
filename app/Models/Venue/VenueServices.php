<?php

namespace App\Models\Venue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Venue\VenueServicesReview;

class VenueServices extends Model
{
    use HasFactory;
  	protected $fillable = [ 
      'organizer_id',
      'name',
      'image',
      'description',
      'price',
      'location',
      'status',
    ]; 

    public function VenueServicesReviews()
    {
        return $this->hasMany(VenueServicesReview::class, 'service_id');
    }
    public function getImageUrlAttribute()
    {
        // Adjust the path as per your application structure
        return asset('VenueServices/' . $this->image);
    }
    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'organizer_id');
    }
    public function bookings()
    {
        return $this->hasMany(VenueServicesBooking::class, 'service_id');
    }
    
}
