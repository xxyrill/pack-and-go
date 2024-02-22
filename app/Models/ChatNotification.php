<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatNotification extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'chat_room_id',
        'receiver_id'
    ];
}
