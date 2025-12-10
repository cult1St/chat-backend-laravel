<?php

namespace App\Http\Resources;

use App\Models\Contact;
use App\Models\ConversationParticipant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatsResource extends JsonResource
{

    public function __construct($resource)
    {
        return parent::__construct($resource);
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $conversationId = $this->resource?->conversation_id;
        $userId = $this->resource?->user_id;

        ///get other participants and return them instead as chat objects
        $otherPaticipant = ConversationParticipant::where('conversation_id', $conversationId)
            ->whereNot('user_id', $userId)
            ->with('user')
            ->first();
        $contact = Contact::where('invitee_id', $otherPaticipant->user?->id)->where('inviter_id', $userId)->first();
        if(!$otherPaticipant) return [];
        return [
            'id' => $conversationId,
            'display_name' => $contact ? $contact->name : $otherPaticipant->user?->phone,
            'full_name' => $contact ? $contact->name : $otherPaticipant->user?->first_name ." ". $otherPaticipant->user?->last_name,
            'phone' => $otherPaticipant->user?->phone,
            'image' => $otherPaticipant->user?->profile_photo_path,
            'last_message' => $this->resource?->conversation->last_message
        ];

    }
}
