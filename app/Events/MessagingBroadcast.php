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
    public $user_id;
    public $reciever_user_id;
    /**
     * Create a new event instance.
     */
    public function __construct(string $user_id, string $message, string $chat_room_id, string $reciever_user_id)
    {
        $this->message = $message;
        $this->chat_room_id = $chat_room_id;
        $this->user_id = $user_id;
        $this->reciever_user_id = $reciever_user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('user_id.'.$this->user_id),
        ];
    }
    
    public function broadcastAs(): string
    {
        return 'chat';
    }
}
