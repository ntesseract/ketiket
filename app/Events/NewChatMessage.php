<?php
// app/Events/NewChatMessage.php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chatMessage;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessage $chatMessage)
    {
        $this->chatMessage = $chatMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast to both user and admin channels
        $channels = [
            new PrivateChannel('chat.user.' . $this->chatMessage->user_id),
        ];
        
        // Add admin channel if message is from user
        if (!$this->chatMessage->is_from_admin && $this->chatMessage->admin_id) {
            $channels[] = new PrivateChannel('chat.admin.' . $this->chatMessage->admin_id);
        }
        
        // Also broadcast to general admin channel
        if (!$this->chatMessage->is_from_admin) {
            $channels[] = new PrivateChannel('chat.admin');
        }
        
        return $channels;
    }
    
    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->chatMessage->id,
            'user_id' => $this->chatMessage->user_id,
            'admin_id' => $this->chatMessage->admin_id,
            'message' => $this->chatMessage->message,
            'is_from_admin' => $this->chatMessage->is_from_admin,
            'created_at' => $this->chatMessage->created_at->format('Y-m-d H:i:s'),
            'user_name' => $this->chatMessage->user->name ?? 'User',
            'admin_name' => $this->chatMessage->admin->name ?? 'Admin',
        ];
    }
}