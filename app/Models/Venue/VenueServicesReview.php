<?php

namespace App\Models\Venue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Customer;

class VenueServicesReview extends Model
{
    use HasFactory;
 	protected $table = 'venue_services_reviews';
    protected $fillable = ['service_id', 'customer_id', 'rating', 'review']; 
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id','id');
    }
    
}
