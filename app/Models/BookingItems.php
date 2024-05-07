<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingItems extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'booking_id',
        'item',
        'quantity',
        'height',
        'weight'
    ];
}
