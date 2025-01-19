<?php

namespace App\Models\Venue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Venue\Venue;
use App\Models\Customer;
use App\Models\Organizer;
use App\Models\User;


class VenueBooking extends Model
{
    use HasFactory;
    
    protected $fillable = [ 
        'booking_id',
        'venue_id',
        'organizer_id',
        'customer_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'guests',
        'status',
        'description',
        'payment',
        'paid_amount',
        'amount',
    ]; 

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function user()
{
    return $this->belongsTo(User::class, 'customer_id');
}

    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'organizer_id');
    }

    public function installments()
    {
        return $this->hasMany(\App\Models\Venue\Installment::class, 'booking_id');
    }

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'guests' => 'array', // Ensure guests field is handled as an array if applicable
    ];

}
