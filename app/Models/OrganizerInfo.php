<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizerInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id',
        'organizer_id',
        'name',
        'shop_name',
        'country',
        'city',
        'state',
        'zip_code',
        'address',
        'details',
        'designation',
        'gstin',
        'uin',
        'pan',
        'aadhar',
        'upi',
        'bank',
        'account_number',
        'ifsc',
        'account_holder_name',
        'branch',
    ];
}
