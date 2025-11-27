<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_plans', function (Blueprint $table) {
            $table->json('wizard_data')->nullable()->after('version');
            $table->boolean('wizard_completed')->default(false)->after('wizard_data');
        });
    }

    public function down(): void
    {
        Schema::table('business_plans', function (Blueprint $table) {
            $table->dropColumn(['wizard_data', 'wizard_completed']);
        });
    }
};
