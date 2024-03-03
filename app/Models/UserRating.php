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
    public function customer() {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }
    public function comment() {
        return $this->hasOne(UserRatingComment::class, 'user_rating_id', 'id')->latest();
    }
}
