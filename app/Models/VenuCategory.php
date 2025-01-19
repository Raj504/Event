<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenuCategory extends Model
{
    protected $table = 'venue_categories';
    protected $primaryKey = 'id';

    protected $fillable = ['name', 'image'];

    // Specify which attributes should be hidden for arrays
    protected $hidden = ['created_at', 'updated_at'];
}
