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
        Schema::create('plan_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_plan_id')->constrained()->onDelete('cascade');

            // Version number
            $table->integer('version_number');

            // Version name
            $table->string('version_name')->nullable()->comment('e.g., Draft 1, Final, Updated');

            // Saved data (Snapshot)
            $table->json('snapshot')->comment('Complete plan data at this version');

            // Changes summary
            $table->text('changes_summary')->nullable();

            // Created by
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('business_plan_id', 'idx_plan');
            $table->index('version_number', 'idx_version');
            $table->unique(['business_plan_id', 'version_number'], 'unique_plan_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_versions');
    }
};
