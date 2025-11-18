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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('industry_type', 100)->comment('restaurant, ecommerce, tech, service');
            $table->text('description')->nullable();

            // Template structure (JSON)
            $table->json('structure')->comment('Chapter structure and order');

            // AI prompts (JSON)
            $table->json('ai_prompts')->nullable()->comment('AI prompts for each chapter');

            // Custom questions
            $table->json('custom_questions')->nullable()->comment('Industry-specific questions');

            // Settings
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);

            // Template thumbnail
            $table->string('thumbnail')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('industry_type', 'idx_industry');
            $table->index('slug', 'idx_slug');
            $table->index('is_active', 'idx_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
