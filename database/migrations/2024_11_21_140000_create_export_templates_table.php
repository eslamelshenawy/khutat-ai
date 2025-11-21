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
        Schema::create('export_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();

            // Branding settings
            $table->string('logo_path')->nullable();
            $table->string('primary_color')->default('#1F4788');
            $table->string('secondary_color')->default('#0D2847');
            $table->string('accent_color')->default('#FFD700');

            // Font settings
            $table->string('font_family')->default('Arial');
            $table->integer('font_size_base')->default(12);

            // Layout settings
            $table->boolean('include_header')->default(true);
            $table->boolean('include_footer')->default(true);
            $table->boolean('include_page_numbers')->default(true);
            $table->boolean('include_table_of_contents')->default(true);

            // Custom text
            $table->text('header_text')->nullable();
            $table->text('footer_text')->nullable();
            $table->string('company_name')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Template type
            $table->enum('template_type', ['pdf', 'word', 'powerpoint', 'all'])->default('all');

            // Status
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_templates');
    }
};
