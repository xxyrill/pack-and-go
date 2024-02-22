<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Ramsey\Uuid\Type\Integer;

class MessagingBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $chat_room_id;
    /**
     * Create a new event instance.
     */
    public function __construct(string $message, string $chat_room_id)
    {
        $this->message = $message;
        $this->chat_room_id = $chat_room_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $val = 'room-'.$this->chat_room_id;
        // return ['public'];
        return [
            new Channel($val),
        ];
    }
    
    public function broadcastAs(): string
    {
        return 'chat';
    }
}
