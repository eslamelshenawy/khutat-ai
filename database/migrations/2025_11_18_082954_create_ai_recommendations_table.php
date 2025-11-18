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
        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('chapter_id')->nullable()->constrained()->onDelete('cascade');

            // Recommendation type
            $table->enum('recommendation_type', ['improvement', 'warning', 'suggestion', 'missing_info']);

            // Priority
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');

            // Title and description
            $table->string('title');
            $table->text('description');

            // Suggested action
            $table->text('suggested_action')->nullable();

            // Status
            $table->enum('status', ['pending', 'applied', 'dismissed', 'ignored'])->default('pending');

            // Applied by
            $table->foreignId('applied_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('applied_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('business_plan_id', 'idx_plan');
            $table->index('status', 'idx_status');
            $table->index('priority', 'idx_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_recommendations');
    }
};
