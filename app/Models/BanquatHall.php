<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanquatHall extends Model
{
    use HasFactory;

    protected $primarykey = 'id'; 
    protected $table = 'banquat_hall'; 
    protected $guarded = []; 
}
