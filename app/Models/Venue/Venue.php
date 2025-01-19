<?php

namespace App\Models\Venue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Organizer;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'type',
        'category',
        'name',
        'description',
        'price',
        'veg_price',
        'non_veg_price',
        'capacity',
        'total_rooms',
        'parking',
        'ac',
        'wifi',
        'decoration',
        'bar',
        'location',
        'status',
        'cancellation_policy',
    ];

    // Define the relationship to VenueImages
    public function venueImages()
    {
        return $this->hasMany(VenueImages::class);
    }

    // Define the relationship to VenueReview
    public function venueReviews()
    {
        return $this->hasMany(VenueReview::class);
    }

    // Define the relationship to VenueType
    public function venueType()
    {
        return $this->belongsTo(VenueType::class, 'type');
    }

    // Define the relationship to VenueCategory
    public function venueCategory()
    {
        return $this->belongsTo(VenueCategory::class, 'category');
    }
    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'organizer_id');
    }
    public function bookings()
    {
        return $this->hasMany(VenueBooking::class, 'venue_id');
    }
}