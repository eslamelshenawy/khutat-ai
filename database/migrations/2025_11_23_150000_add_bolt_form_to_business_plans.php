<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_plans', function (Blueprint $table) {
            $table->foreignId('bolt_form_id')->nullable()->after('template_id')->constrained('bolt_forms')->nullOnDelete();
            $table->foreignId('bolt_response_id')->nullable()->after('bolt_form_id')->constrained('bolt_responses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('business_plans', function (Blueprint $table) {
            $table->dropForeign(['bolt_form_id']);
            $table->dropForeign(['bolt_response_id']);
            $table->dropColumn(['bolt_form_id', 'bolt_response_id']);
        });
    }
};
