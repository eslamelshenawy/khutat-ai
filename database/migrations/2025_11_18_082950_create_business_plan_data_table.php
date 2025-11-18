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
        Schema::create('business_plan_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_plan_id')->constrained()->onDelete('cascade');

            // Key and value
            $table->string('field_key', 100)->comment('e.g., target_market, capital, team_size');
            $table->text('field_value');
            $table->string('field_type', 50)->default('text')->comment('text, number, json, date');

            // Wizard step
            $table->integer('wizard_step')->nullable()->comment('Which step this data belongs to');

            // Validation
            $table->boolean('is_validated')->default(false);
            $table->timestamp('validated_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('business_plan_id', 'idx_plan');
            $table->index('field_key', 'idx_field');
            $table->unique(['business_plan_id', 'field_key'], 'unique_plan_field');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_plan_data');
    }
};
