<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRatingComment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_rating_id',
        'user_id',
        'comment'
    ];
    public function service() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
