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
        Schema::create('financial_simulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_plan_id')->constrained()->onDelete('cascade');

            // Scenario type
            $table->enum('scenario_type', ['optimistic', 'realistic', 'pessimistic']);

            // Financial data (JSON)
            $table->json('data')->comment('Financial projections, revenue, expenses, etc.');

            // Analysis results
            $table->integer('break_even_point')->nullable()->comment('Month number');
            $table->decimal('roi_percentage', 10, 2)->nullable();
            $table->decimal('total_revenue', 15, 2)->nullable();
            $table->decimal('total_expenses', 15, 2)->nullable();
            $table->decimal('net_profit', 15, 2)->nullable();

            // Chart data
            $table->json('chart_data')->nullable()->comment('Data for graphs');

            // AI Generated
            $table->boolean('is_ai_generated')->default(true);
            $table->string('ai_model', 50)->nullable();

            $table->timestamps();

            // Indexes
            $table->index('business_plan_id', 'idx_plan');
            $table->index('scenario_type', 'idx_scenario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_simulations');
    }
};
