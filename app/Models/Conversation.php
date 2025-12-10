<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'type',
        'status',
        'users_count',
        'last_message_sent'
    ];

    protected $casts = [
        'last_message_sent' => 'object'
    ];

    public function participants(){
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages(){
        return $this->hasMany(ConversationMessage::class);
    }
}
