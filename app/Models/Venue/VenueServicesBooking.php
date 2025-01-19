<?php

namespace App\Models\Venue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Venue\VenueServices;
use App\Models\Customer;
use App\Models\Venue\InstallmentService;
use App\Models\Organizer;

class VenueServicesBooking extends Model
{
    use HasFactory;
  	protected $fillable = [ 
      'booking_id',
      'service_id',
      'customer_id',
      'booking_date',
      'start_time',
      'end_time',
      'guests',
      'status',
      'description',
      'payment',
      'amount',
    ]; 

    public function service()
    {
        return $this->belongsTo(VenueServices::class, 'service_id');
    }
    public function installment_services()
    {
        return $this->hasMany(InstallmentService::class, 'booking_id');
    }
    

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'organizer_id');
    }
    public function installments()
    {
        return $this->hasMany(InstallmentService::class, 'booking_id');
    }
    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'guests' => 'array',
    ];
}
