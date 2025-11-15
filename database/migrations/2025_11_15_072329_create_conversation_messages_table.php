<?php

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conversation::class);
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(ConversationMessage::class, 'reply_id')->nullable();
            $table->longText('message')->nullable();
            $table->enum('type', ['message', 'photo', 'file'])->default('message');
            $table->enum('status', ['active', 'inactive', 'blocked', 'deleted'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_messages');
    }
};
