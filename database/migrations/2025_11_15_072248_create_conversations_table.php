<?php

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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->foreignIdFor(User::class, 'created_by');
            $table->enum('type', ['chat', 'group', 'channel'])->default('chat');
            $table->enum('status', ['active', 'inactive', 'blocked', 'deleted'])->default('active');
            $table->integer('users_count')->default(0);
            $table->json('last_message_sent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
