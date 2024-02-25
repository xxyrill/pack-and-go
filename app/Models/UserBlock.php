<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBlock extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'blocked_user_id'
    ];
}
