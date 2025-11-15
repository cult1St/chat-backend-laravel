<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'user_id',
        'reply_id',
        'message',
        'type',
        'status'
    ];
}
