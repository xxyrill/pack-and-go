<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'subscription_period',
        'price',
        'status',
        'type'
    ];
    public function subscriptionInclusion() {
        return $this->hasMany(SubscriptionInclusion::class, 'subscription_id', 'id');
    }
}
