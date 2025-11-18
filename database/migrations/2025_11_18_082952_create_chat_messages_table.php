<?php

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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Message
            $table->text('message');
            $table->boolean('is_user')->default(true)->comment('true = user, false = AI');

            // AI Response Info
            $table->string('ai_model', 50)->nullable();
            $table->integer('tokens_used')->nullable();
            $table->integer('processing_time_ms')->nullable();

            // Context (optional)
            $table->json('context')->nullable()->comment('Conversation context');

            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('business_plan_id', 'idx_plan');
            $table->index('created_at', 'idx_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
