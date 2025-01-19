<?php

namespace App\Models\Venue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueType extends Model
{
    use HasFactory;
 	//protected $table = 'venue_categories';
    protected $fillable = ['type']; 

    public function venues()
    {
        return $this->hasMany(Venue::class, 'type');
    }
}
