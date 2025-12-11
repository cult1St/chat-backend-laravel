<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use App\Models\ConversationMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ConversationMessage $conversationMessage;

    /**
     * Create a new event instance.
     */
    public function __construct(ConversationMessage $conversationMessage)
    {
        // Important: SerializesModels ensures model IDs are stored, not objects.
        $this->conversationMessage = $conversationMessage;
    }

    /**
     * The channel the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Find the receiver (the participant that isn’t the sender)
        $receiverId = $this->conversationMessage
            ->conversation
            ->participants()
            ->where('user_id', '!=', $this->conversationMessage->user_id)
            ->value('user_id');

        return [
            new PrivateChannel("chat-messages.{$receiverId}")
        ];
    }

    /**
     * Event name on the frontend (Echo/Reverb)
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Payload sent to the frontend
     */
    public function broadcastWith(): array
    {
        return (new MessageResource($this->conversationMessage))->toArray(request());
    }
}
