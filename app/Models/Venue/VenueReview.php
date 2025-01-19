<?php

namespace App\Models\Venue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Customer;

class VenueReview extends Model
{
    use HasFactory;
 	protected $table = 'venue_reviews';
    protected $fillable = ['venue_id', 'customer_id', 'rating', 'review']; 
    public function customer()
    {
        // Ensure the relationship refers to the correct table and field
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }
    
}
