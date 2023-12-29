<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'movie_schedule_id',
        'seat_id',
        'date_screening'
    ];

    use HasFactory;
}
