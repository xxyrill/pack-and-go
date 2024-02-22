<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatRooms extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'booking_id',
        'appointer_id',
        'customer_id'
    ];
    public function chats() {
        return $this->hasMany(Messaging::class, 'chat_room_id', 'id');
    }
    public function appointer() {
        return $this->hasOne(User::class, 'id','appointer_id');
    }
    public function booking() {
        return $this->hasOne(Booking::class, 'id','booking_id');
    }
    public function customer() {
        return $this->hasOne(User::class, 'id','customer_id');
    }
    public function chatNotifications() {
        return $this->hasMany(ChatNotification::class, 'chat_room_id','id');
    }
}
