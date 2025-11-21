<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_plan_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('shared_by')->constrained('users')->onDelete('cascade');
            $table->string('token')->unique();
            $table->enum('type', ['public', 'private'])->default('public');
            $table->string('password')->nullable();
            $table->enum('permission', ['view', 'comment', 'edit'])->default('view');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('view_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('token');
            $table->index(['business_plan_id', 'is_active']);
        });

        Schema::create('business_plan_share_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('share_id')->constrained('business_plan_shares')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->timestamp('viewed_at');
            $table->json('metadata')->nullable();

            $table->index('share_id');
            $table->index('viewed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_plan_share_views');
        Schema::dropIfExists('business_plan_shares');
    }
};
