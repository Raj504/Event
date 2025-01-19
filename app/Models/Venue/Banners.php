<?php

namespace App\Models\Venue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banners extends Model
{
    use HasFactory;
 	protected $table = 'banners';
    protected $fillable = ['image', 'image_url']; 
}
