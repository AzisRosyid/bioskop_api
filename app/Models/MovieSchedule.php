<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieSchedule extends Model
{
    protected $fillable = [
        'cinema_id',
        'movie_id',
        'start_time',
        'end_time'
    ];

    use HasFactory;
}
