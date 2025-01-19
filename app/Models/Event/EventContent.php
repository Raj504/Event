<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventContent extends Model
{
  use HasFactory;

  protected $fillable = [
    'event_id',
    'event_category_id',
    'title',
    'address',
    'country',
    'state',
    'city',
    'zip_code',
    'description',
    'meta_keywords',
    'meta_description',
    'google_calendar_id',
    'refund_policy',
    'features',
    'book_date',


  ];

  public function tickets()
  {
    return $this->hasMany(Ticket::class, 'event_id', 'event_id');
  }

  // public function event()
  // {
  //     return $this->belongsTo(Event::class);
  // }

}
