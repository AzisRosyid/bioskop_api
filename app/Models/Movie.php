<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'duration',
        'release_date',
        'genre',
        'description',
        'price'
    ];

    use HasFactory;
}
