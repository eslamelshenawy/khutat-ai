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
        Schema::create('financial_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_plan_id')->constrained()->onDelete('cascade');
            $table->integer('year');

            // Income Statement
            $table->decimal('revenue', 15, 2)->default(0);
            $table->decimal('cost_of_goods_sold', 15, 2)->default(0);
            $table->decimal('gross_profit', 15, 2)->default(0);
            $table->decimal('operating_expenses', 15, 2)->default(0);
            $table->decimal('operating_income', 15, 2)->default(0);
            $table->decimal('net_income', 15, 2)->default(0);

            // Cash Flow Statement
            $table->decimal('cash_inflow', 15, 2)->default(0);
            $table->decimal('cash_outflow', 15, 2)->default(0);
            $table->decimal('net_cash_flow', 15, 2)->default(0);

            // Balance Sheet
            $table->decimal('assets', 15, 2)->default(0);
            $table->decimal('liabilities', 15, 2)->default(0);
            $table->decimal('equity', 15, 2)->default(0);

            $table->timestamps();

            $table->unique(['business_plan_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_data');
    }
};
