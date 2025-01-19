<?php

namespace App\Models\Venue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_percentage',
        'max_usage',
        'used_count',
        'expires_at',
    ];

    protected $dates = ['expires_at'];

    public function isValid()
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    public function canBeUsed()
    {
        return $this->isValid() && $this->used_count < $this->max_usage;
    }
}
