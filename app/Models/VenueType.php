<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueType extends Model
{
    protected $table = 'venue_types';
    protected $primaryKey = 'id';

    protected $fillable = ['type'];
    // Specify which attributes should be hidden for arrays
    protected $hidden = ['created_at', 'updated_at'];
}
