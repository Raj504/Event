<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $table = 'venues';
    protected $primaryKey = 'id';

    protected $fillable = ['name', 'organizer_id','type','category','description','price','veg_price','non_veg_price','capacity','total_rooms','parking','wifi','ac','bar','decoration','location','cancellation_policy'];

    // Specify which attributes should be hidden for arrays
    protected $hidden = ['created_at', 'updated_at'];


                public function bookings()
                {
                    return $this->hasMany(VenueBooking::class);
                }


}
