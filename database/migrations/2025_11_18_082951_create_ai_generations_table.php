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
        Schema::create('ai_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('chapter_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Generation type
            $table->enum('generation_type', ['chapter', 'suggestion', 'analysis', 'improvement', 'chat']);

            // Prompt & Response
            $table->text('prompt');
            $table->longText('response');

            // AI Model Info
            $table->string('model_used', 50)->default('gpt-3.5-turbo');
            $table->integer('tokens_used')->nullable();
            $table->decimal('cost', 10, 6)->nullable()->comment('Cost in USD');

            // Processing time
            $table->integer('processing_time_ms')->nullable();

            // Status
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->text('error_message')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('business_plan_id', 'idx_plan');
            $table->index('user_id', 'idx_user');
            $table->index('generation_type', 'idx_type');
            $table->index('created_at', 'idx_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_generations');
    }
};
