<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;
    protected $primarykey = 'id';
    protected $table = 'images';
    protected $fillable = [
        'name',
        'extension',
        'model',
        'model_id',
        'path',
    ];
}
