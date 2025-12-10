<?php

namespace App\Http\Controllers;

use App\ApiResponder;
use App\Http\Resources\ChatsResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use App\Services\ChatService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class ChatController extends Controller
{

    public function __construct(
        private ChatService $chatService
    ){}
    use ApiResponder;
    public function index(){
        $user = Auth::user();

        $data = $this->chatService->getUserChats($user->id, request()->get('take', 50));

        return $this->successResponse($data);
    }

    public function messagesWith($phone){
        $user = Auth::user();

        try{
            $messages = $this->chatService->getMessagesWithUser(
                $user->id,
                $phone,
                request()->get('take', 50)
            );
        }catch(InvalidArgumentException $e){
            return $this->errorResponse($e->getMessage(), 500);
        }catch(Exception $e){
            return $this->successResponse([], $e->getMessage());
        }

        return $this->successResponse(MessageResource::collection($messages));

    }

    public function sendMessage(Request $request){
        $validated = $request->validate([
            "phone" => "required|string|exists:users,phone",
            "message" => "required|string",
        ]);

        try{
            $message = $this->chatService->sendMessage(
                $validated['message'],
                $validated['phone']
            );
        }catch(Exception $e){
            return $this->errorResponse($e->getMessage(), 500);
        }

        return $this->successResponse(new MessageResource($message), "Message Sent Successfully");
    }
}
