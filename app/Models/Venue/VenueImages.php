<?php

namespace App\Models\Venue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueImages extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'venue_id',
        'image_url',
    ]; 
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}   
