<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use App\Models\ConversationMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ConversationMessage $conversationMessage;

    /**
     * Create a new event instance.
     */
    public function __construct(ConversationMessage $conversationMessage)
    {
        $this->conversationMessage = $conversationMessage;

        Log::info('MessageSent event constructed', [
            'message_id' => $conversationMessage->id ?? null,
            'conversation_id' => $conversationMessage->conversation_id ?? null,
            'sender_id' => $conversationMessage->user_id ?? null,
        ]);
    }

    /**
     * The channel the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Find the receiver participant (not the sender)
        $receiverParticipant = $this->conversationMessage
            ->conversation
            ->participants()
            ->where('user_id', '!=', $this->conversationMessage->user_id)
            ->with('user') // Eager load user for phone access
            ->first();

        if (!$receiverParticipant || !$receiverParticipant->user || !$receiverParticipant->user->phone) {
            Log::error('MessageSent: Receiver phone not found, skipping broadcast', [
                'message_id' => $this->conversationMessage->id ?? null,
                'receiver_participant_id' => $receiverParticipant->id ?? null,
            ]);
            return []; // No channels to broadcast on
        }

        $receiverPhone = $receiverParticipant->user->phone;
        $channelName = "chat-messages.{$receiverPhone}";

        Log::info('MessageSent broadcastOn resolved channel', [
            'receiver_phone' => $receiverPhone,
            'channel' => $channelName,
        ]);

        return [
            new PrivateChannel($channelName)
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
        try {
            $payload = (new MessageResource($this->conversationMessage))->toArray(null);

            Log::info('MessageSent broadcasting payload', [
                'message_id' => $this->conversationMessage->id ?? null,
                'payload' => $payload, // Log full payload for debugging; remove in production if sensitive
            ]);

            return $payload;
        } catch (\Exception $e) {
            Log::error('MessageSent: Failed to generate payload', [
                'message_id' => $this->conversationMessage->id ?? null,
                'error' => $e->getMessage(),
            ]);
            return []; // Return empty payload to avoid crashes
        }
    }
}
