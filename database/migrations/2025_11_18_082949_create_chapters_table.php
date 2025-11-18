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
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_plan_id')->constrained()->onDelete('cascade');

            // Chapter information
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable()->comment('Rich HTML content');

            // Chapter type
            $table->string('chapter_type', 100)->comment('executive_summary, market_analysis, etc.');

            // Sort order
            $table->integer('sort_order')->default(0);

            // Chapter status
            $table->enum('status', ['empty', 'draft', 'ai_generated', 'completed'])->default('empty');

            // AI Generation
            $table->boolean('is_ai_generated')->default(false);
            $table->text('ai_prompt')->nullable();
            $table->string('ai_model_used', 50)->nullable()->comment('gpt-4, gpt-3.5-turbo');
            $table->timestamp('ai_generated_at')->nullable();

            // Word count
            $table->integer('word_count')->default(0);

            // Lock for collaboration
            $table->foreignId('locked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('locked_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('business_plan_id', 'idx_plan');
            $table->index('chapter_type', 'idx_type');
            $table->index('sort_order', 'idx_order');
            $table->unique(['business_plan_id', 'chapter_type'], 'unique_plan_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
