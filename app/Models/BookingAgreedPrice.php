<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingAgreedPrice extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'booking_id',
        'service_user_id',
        'price',
        'selected'
    ];
    public function driver() {
        return $this->hasOne(User::class, 'id', 'service_user_id');
    }
}
