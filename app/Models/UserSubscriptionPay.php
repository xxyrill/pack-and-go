<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSubscriptionPay extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_subscription_id',
        'date_pay'
    ];
}
