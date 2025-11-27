<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wizard_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان الخطوة (مثل: الشركاء الرئيسيين)
            $table->text('description')->nullable(); // وصف الخطوة
            $table->string('icon')->nullable(); // أيقونة الخطوة
            $table->integer('order')->default(0); // ترتيب الخطوة
            $table->boolean('is_active')->default(true); // نشط/غير نشط
            $table->timestamps();
        });

        Schema::create('wizard_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wizard_step_id')->constrained()->onDelete('cascade');
            $table->string('label'); // نص السؤال
            $table->text('help_text')->nullable(); // نص المساعدة
            $table->enum('type', ['text', 'textarea', 'select', 'radio', 'checkbox', 'number', 'date'])->default('text');
            $table->json('options')->nullable(); // للـ select, radio, checkbox
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0); // ترتيب السؤال داخل الخطوة
            $table->string('field_name'); // اسم الحقل في DB (مثل: key_partners)
            $table->json('validation_rules')->nullable(); // قواعد validation إضافية
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wizard_questions');
        Schema::dropIfExists('wizard_steps');
    }
};
