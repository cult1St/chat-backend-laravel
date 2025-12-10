<?php

namespace App\Services;

use App\Http\Resources\ChatsResource;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class ChatService
{

    public function getUsersChats(int $userId, int $take = 10)
    {
        $chats = ConversationParticipant::where('user_id', $userId)
            ->with('conversation')
            ->whereHas('conversaton', function ($q) {
                $q->where('type', 'chat');
            })
            ->orderBy('id', 'desc')
            ->paginate();
    }

    public function sendMessage(string $message, string $phone)
    {
        $user = Auth::user();
        $otherUser = User::where('phone', $phone)->first();

        //find or create conversation
        $conversation = Conversation::where('type', 'chat')
            ->whereHas('participants', function ($query) use ($user, $otherUser) {
                $query->whereIn('user_id', [$user->id, $otherUser->id]);
            })
            ->first();
        if (!$conversation) {
            //create conversation
            $conversation = Conversation::create([
                "type" => "chat",
                "last_message" => null,
                "created_by" => $user->id,
                "users_count" => 2,
            ]);
            //add participants
            ConversationParticipant::create([
                "conversation_id" => $conversation->id,
                "user_id" => $user->id,
            ]);
            ConversationParticipant::create([
                "conversation_id" => $conversation->id,
                "user_id" => $otherUser->id,
            ]);
        }

        //create message
        $message = $conversation->messages()->create([
            "user_id" => $user->id,
            "message" => $message,
            "status" => "sent",
        ]);

        //update last message in conversation
        $conversation->last_message_sent = $message->message;
        $conversation->save();
        return $message;
    }

    public function getUserChats(int $userId, int $take = 10)
    {
        $chats = ConversationParticipant::where('user_id', $userId)
            ->whereHas('conversation', function ($query) {
                $query->where('type', 'chat');
            })
            ->paginate(request()->get('take', 20));

        $data = ChatsResource::collection($chats);
        return $data;
    }

    public function getMessagesWithUser(int $userId, string $phone, int $take = 10)
    {
        $otherUser = User::where('phone', $phone)->first();
        if (!$otherUser) {
            throw new InvalidArgumentException("User with phone {$phone} not found");
        }
        $conversation = Conversation::where('type', 'chat')
            ->whereHas('participants', function ($query) use ($userId, $otherUser) {
                $query->whereIn('user_id', [$userId, $otherUser->id]);
            })
            ->first();
        if (!$conversation) {
            throw new Exception("No conversation found between users");
        }
        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->paginate(request()->get('take', 20));
        return $messages;
    }
}
