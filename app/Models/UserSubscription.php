<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSubscription extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'subscription_id',
        'user_id',
        'status'
    ];

    public function subscription() {
        return $this->hasOne(Subscription::class, 'id', 'subscription_id' );
    }
    public function user() {
        return $this->hasOne(User::class, 'user_id');
    }
}
