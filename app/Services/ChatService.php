<?php

namespace App\Services;

use App\Models\ConversationParticipant;

class ChatService
{

    public function getUsersChats(int $userId, int $take = 10)
    {
        $chats = ConversationParticipant::where('user_id', $userId)
            ->with('conversation')
            ->whereHas('conversaton', function($q){
                $q->where('type', 'chat');
            })
            ->orderBy('id', 'desc')
            ->paginate();
    }
}
