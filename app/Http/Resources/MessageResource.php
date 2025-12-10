<?php

namespace App\Http\Resources;

use App\Models\Contact;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class MessageResource extends JsonResource
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
        //create a formatted time from the created at
        $userId = Auth::id();
        //get the creator of the message
        $creator = Conversation::find($this->resource?->conversation_id)
            ->participants()
            ->where('user_id', $this->resource?->user_id)
            ->with('user')
            ->first();

        $direction = $this->resource?->user_id == $userId ? "outgoing" :  "incoming";
        if($direction == "outgoing"){
             $contact = Contact::where('inviter_id', $creator->user?->id)->where('invitee_id', $userId)->first();
        }else{
             $contact = Contact::where('invitee_id', $creator->user?->id)->where('inviter_id', $userId)->first();
        }

        $chat = [
            "id" => $creator?->id,
            'display_name' => $contact ? $contact->name : $creator->user?->phone,
            'full_name' => $contact ? $contact->name : $creator->user?->first_name . " " . $creator->user?->last_name,
            'phone' => $creator->user?->phone,
            'image' => $creator->user?->profile_photo_path,
        ];

        if($this->resource->status != 'seen' && $this->resource->user_id != $userId){
            $this->resource->update(['status' => 'seen']);
        }
        return [
            "direction" => $direction ,
            "text"  => $this->resource?->message,
            "time" => $this->getFormattedTime($this->resource?->created_at),
            "chat_user" => $chat,
            "status" => $this->resource?->status,
        ];
    }

    protected function getFormattedTime($timestamp)
    {
        $toTime = strtotime($timestamp);
        //if now or less than a minute ago, return "just now"
        $fromTime = strtotime(now());
        $timeDiff = $fromTime - $toTime;
        if ($timeDiff < 60) {
            return "just now";
        }
        //if less than today return time in h:i A format
        if (date('Y-m-d', $toTime) == date('Y-m-d', $fromTime)) {
            return date('h:i A', $toTime);
        }
        //if yesterday return "yesterday with time"
        if (date('Y-m-d', $toTime) == date('Y-m-d', strtotime('-1 day', $fromTime))) {
            return "yesterday at " . date('h:i A', $toTime);
        }
        //if within this year return date in M d format
        if (date('Y', $toTime) == date('Y', $fromTime)) {
            return date('M d', $toTime);
        }
        return date('M d, Y', $toTime);
    }
}
