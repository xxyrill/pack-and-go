<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Messaging extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'chat_room_id',
        'user_id',
        'message'
    ];
}
