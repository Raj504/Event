<?php

namespace App\Models\Venue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentService extends Model
{
    use HasFactory;

    protected $table = 'installments_services';


    protected $fillable = [
        'booking_id',
        'customer_id',
        'amount',
        'status',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    
}
