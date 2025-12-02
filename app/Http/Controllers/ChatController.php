<?php

namespace App\Http\Controllers;

use App\ApiResponder;
use App\Http\Resources\ChatsResource;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    use ApiResponder;
    public function index(){
        $user = Auth::user();

        $chats = ConversationParticipant::where('user_id', $user->id)
            ->whereHas('conversation', function($query){
                $query->where('type', 'chat');
            })
            ->paginate(request()->get('take', 20));

        $data = ChatsResource::collection($chats);

        return $this->successResponse($data);
    }
}
