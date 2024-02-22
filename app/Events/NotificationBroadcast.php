<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $chat_room_id;
    public $chat_creator;
    /**
     * Create a new event instance.
     */
    public function __construct(string $user_id, string $chat_room_id, string $chat_creator)
    {
        $this->user_id = $user_id;
        $this->chat_room_id = $chat_room_id;
        $this->chat_creator = $chat_creator;
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
        return 'notification';
    }
}
