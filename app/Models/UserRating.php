<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRating extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'service_user_id',
        'customer_id',
        'booking_id',
        'rate',
        'additional_comment',
    ];
}
