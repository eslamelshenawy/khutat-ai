<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wizard_steps', function (Blueprint $table) {
            // Add bolt_form_id to link each wizard step with a Bolt form
            $table->unsignedBigInteger('bolt_form_id')->nullable()->after('is_active');

            // Add AI suggestion settings
            $table->boolean('enable_ai_suggestions')->default(true)->after('bolt_form_id');
            $table->text('ai_suggestion_prompt')->nullable()->after('enable_ai_suggestions');

            // Add index for faster lookups
            $table->index('bolt_form_id');
        });
    }

    public function down(): void
    {
        Schema::table('wizard_steps', function (Blueprint $table) {
            $table->dropIndex(['bolt_form_id']);
            $table->dropColumn(['bolt_form_id', 'enable_ai_suggestions', 'ai_suggestion_prompt']);
        });
    }
};
