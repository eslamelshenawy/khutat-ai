@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('export-templates.index') }}" class="text-blue-600 hover:text-blue-800">
            ← العودة إلى القوالب
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">إنشاء قالب تصدير جديد</h1>
    </div>

    <form action="{{ route('export-templates.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-6">
        @csrf

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">اسم القالب *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">الوصف</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-bold mb-2">اللون الأساسي *</label>
                <input type="color" name="primary_color" value="{{ old('primary_color', '#1F4788') }}" required
                       class="w-full h-12 border border-gray-300 rounded-lg cursor-pointer">
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-2">اللون الثانوي *</label>
                <input type="color" name="secondary_color" value="{{ old('secondary_color', '#0D2847') }}" required
                       class="w-full h-12 border border-gray-300 rounded-lg cursor-pointer">
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-2">لون التمييز *</label>
                <input type="color" name="accent_color" value="{{ old('accent_color', '#FFD700') }}" required
                       class="w-full h-12 border border-gray-300 rounded-lg cursor-pointer">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-bold mb-2">نوع الخط *</label>
                <select name="font_family" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="Arial">Arial</option>
                    <option value="Helvetica">Helvetica</option>
                    <option value="Times New Roman">Times New Roman</option>
                    <option value="Georgia">Georgia</option>
                    <option value="Verdana">Verdana</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-2">حجم الخط *</label>
                <input type="number" name="font_size_base" value="{{ old('font_size_base', 12) }}" min="8" max="20" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">الشعار (اختياري)</label>
            <input type="file" name="logo" accept="image/*"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2">
            <p class="text-sm text-gray-500 mt-1">الحد الأقصى: 2MB</p>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">نوع القالب *</label>
            <select name="template_type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                <option value="all">جميع الأنواع</option>
                <option value="pdf">PDF فقط</option>
                <option value="word">Word فقط</option>
                <option value="powerpoint">PowerPoint فقط</option>
            </select>
        </div>

        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
            <h3 class="font-bold text-gray-700 mb-3">خيارات التخطيط</h3>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="include_header" value="1" checked class="ml-2">
                    <span>تضمين رأس الصفحة</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="include_footer" value="1" checked class="ml-2">
                    <span>تضمين تذييل الصفحة</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="include_page_numbers" value="1" checked class="ml-2">
                    <span>إضافة أرقام الصفحات</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="include_table_of_contents" value="1" checked class="ml-2">
                    <span>إضافة جدول المحتويات</span>
                </label>
            </div>
        </div>

        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="ml-2">
                <span class="font-bold text-gray-700">تعيين كقالب افتراضي</span>
            </label>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-bold">
                إنشاء القالب
            </button>
            <a href="{{ route('export-templates.index') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-bold">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
