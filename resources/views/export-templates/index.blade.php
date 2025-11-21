@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">قوالب التصدير المخصصة</h1>
        <a href="{{ route('export-templates.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            + إنشاء قالب جديد
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if($templates->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($templates as $template)
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $template->name }}</h3>
                    @if($template->is_default)
                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mt-2">
                        القالب الافتراضي
                    </span>
                    @endif
                </div>
                <div class="flex items-center space-x-2 space-x-reverse">
                    <div class="w-6 h-6 rounded" style="background-color: {{ $template->primary_color }}"></div>
                    <div class="w-6 h-6 rounded" style="background-color: {{ $template->secondary_color }}"></div>
                    <div class="w-6 h-6 rounded" style="background-color: {{ $template->accent_color }}"></div>
                </div>
            </div>

            @if($template->description)
            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($template->description, 100) }}</p>
            @endif

            <div class="border-t pt-4 mt-4">
                <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-4">
                    <div>الخط: {{ $template->font_family }}</div>
                    <div>الحجم: {{ $template->font_size_base }}pt</div>
                    <div>النوع:
                        @if($template->template_type === 'pdf') PDF
                        @elseif($template->template_type === 'word') Word
                        @elseif($template->template_type === 'powerpoint') PowerPoint
                        @else جميع الأنواع
                        @endif
                    </div>
                    <div>
                        @if($template->logo_path)
                        <span class="text-green-600">✓ يحتوي على شعار</span>
                        @else
                        <span class="text-gray-400">بدون شعار</span>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('export-templates.edit', $template) }}" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded text-center hover:bg-gray-200 transition">
                        تعديل
                    </a>

                    @if(!$template->is_default)
                    <form action="{{ route('export-templates.set-default', $template) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-blue-100 text-blue-700 px-4 py-2 rounded hover:bg-blue-200 transition">
                            تعيين كافتراضي
                        </button>
                    </form>
                    @endif

                    <form action="{{ route('export-templates.destroy', $template) }}" method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا القالب؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-100 text-red-700 px-4 py-2 rounded hover:bg-red-200 transition">
                            حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12 bg-white rounded-lg shadow">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">لا توجد قوالب تصدير بعد</h3>
        <p class="text-gray-600 mb-4">قم بإنشاء قالب مخصص بألوانك وشعارك الخاص</p>
        <a href="{{ route('export-templates.create') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            إنشاء قالب جديد
        </a>
    </div>
    @endif
</div>
@endsection
