<?php

namespace App\Models\Venue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueCategory extends Model
{
    use HasFactory;
 	protected $table = 'venue_categories';
    protected $fillable = ['name', 'image']; 

    public function venues()
    {
        return $this->hasMany(Venue::class, 'category');
    }
    
}
