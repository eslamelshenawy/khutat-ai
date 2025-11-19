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
        Schema::create('business_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained()->onDelete('set null');

            // Basic information
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();

            // Project type
            $table->enum('project_type', ['new_business', 'existing_expansion', 'franchise', 'startup'])->default('new_business');

            // Industry type
            $table->string('industry_type', 100)->nullable();

            // Plan status
            $table->enum('status', ['draft', 'in_progress', 'review', 'completed', 'archived'])->default('draft');

            // Completion percentage
            $table->tinyInteger('completion_percentage')->default(0)->unsigned();

            // AI evaluation
            $table->tinyInteger('ai_score')->nullable()->unsigned()->comment('Plan Quality Score 1-100');
            $table->text('ai_feedback')->nullable()->comment('AI recommendations');
            $table->timestamp('last_analyzed_at')->nullable();

            // Project information
            $table->string('company_name')->nullable();
            $table->string('company_logo')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();

            // Settings
            $table->string('language', 5)->default('ar')->comment('ar, en, fr');
            $table->boolean('is_public')->default(false);
            $table->boolean('allow_comments')->default(false);

            // Versioning
            $table->integer('version')->default(1);
            $table->foreignId('parent_plan_id')->nullable()->constrained('business_plans')->onDelete('set null');

            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id', 'idx_user');
            $table->index('status', 'idx_status');
            $table->index('template_id', 'idx_template');
            $table->index('created_at', 'idx_created');
            $table->unique(['user_id', 'slug'], 'unique_user_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_plans');
    }
};
