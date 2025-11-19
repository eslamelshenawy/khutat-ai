<x-layouts.app>
    <x-slot name="title">{{ $businessPlan->title }}</x-slot>

    <div class="min-h-screen bg-gray-50 py-8" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <a href="{{ route('business-plans.index') }}" class="text-gray-600 hover:text-gray-900 ml-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </a>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $businessPlan->title }}</h1>
                        </div>
                        <p class="text-lg text-gray-600 mr-10">{{ $businessPlan->company_name }}</p>

                        <div class="flex items-center space-x-reverse space-x-4 mt-4 mr-10">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $businessPlan->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $businessPlan->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $businessPlan->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $businessPlan->status === 'published' ? 'bg-purple-100 text-purple-800' : '' }}">
                                @if($businessPlan->status === 'completed') مكتمل
                                @elseif($businessPlan->status === 'draft') مسودة
                                @elseif($businessPlan->status === 'in_progress') قيد التنفيذ
                                @elseif($businessPlan->status === 'published') منشور
                                @endif
                            </span>

                            <span class="text-sm text-gray-500">
                                نسبة الإكمال: <span class="font-bold text-blue-600">{{ $businessPlan->completion_percentage }}%</span>
                            </span>
                        </div>
                    </div>

                    <div class="flex space-x-reverse space-x-2">
                        @if($businessPlan->status !== 'completed')
                        <a href="{{ route('wizard.steps', $businessPlan) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            متابعة التحرير
                        </a>
                        @endif

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                <div class="py-1">
                                    <a href="{{ route('business-plans.export', [$businessPlan, 'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        تصدير PDF
                                    </a>
                                    <a href="{{ route('business-plans.export', [$businessPlan, 'docx']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        تصدير DOCX
                                    </a>
                                    <form action="{{ route('business-plans.duplicate', $businessPlan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            نسخ الخطة
                                        </button>
                                    </form>
                                    <form action="{{ route('business-plans.destroy', $businessPlan) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="block w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                            حذف الخطة
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $businessPlan->completion_percentage }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Meta Information -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="mr-4">
                            <p class="text-sm text-gray-500">نوع المشروع</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @if($businessPlan->project_type === 'new_business') مشروع جديد
                                @elseif($businessPlan->project_type === 'existing_expansion') توسع مشروع قائم
                                @elseif($businessPlan->project_type === 'franchise') امتياز تجاري
                                @elseif($businessPlan->project_type === 'startup') شركة ناشئة
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div class="mr-4">
                            <p class="text-sm text-gray-500">نوع الصناعة</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $businessPlan->industry_type }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="mr-4">
                            <p class="text-sm text-gray-500">عدد الفصول</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $businessPlan->chapters->count() }} فصل</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="mr-4">
                            <p class="text-sm text-gray-500">تاريخ الإنشاء</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $businessPlan->created_at->format('Y/m/d') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chapters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">الفصول</h2>

                @if($businessPlan->chapters->count() > 0)
                <div class="space-y-4">
                    @foreach($businessPlan->chapters as $chapter)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $chapter->title }}</h3>

                                <div class="flex items-center space-x-reverse space-x-4 text-sm">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                        {{ $chapter->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $chapter->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $chapter->status === 'empty' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $chapter->status === 'ai_generated' ? 'bg-purple-100 text-purple-800' : '' }}">
                                        @if($chapter->status === 'completed') مكتمل
                                        @elseif($chapter->status === 'draft') مسودة
                                        @elseif($chapter->status === 'empty') فارغ
                                        @elseif($chapter->status === 'ai_generated') مولد بالذكاء الاصطناعي
                                        @endif
                                    </span>

                                    @if($chapter->is_ai_generated)
                                    <span class="text-purple-600 flex items-center">
                                        <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13 7H7v6h6V7z"></path>
                                            <path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd"></path>
                                        </svg>
                                        AI
                                    </span>
                                    @endif

                                    @if($chapter->content)
                                    <span class="text-gray-500">
                                        {{ str_word_count(strip_tags($chapter->content)) }} كلمة
                                    </span>
                                    @endif
                                </div>

                                @if($chapter->content && strlen($chapter->content) > 0)
                                <p class="mt-2 text-sm text-gray-600 line-clamp-2">
                                    {{ Str::limit(strip_tags($chapter->content), 150) }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-8">لا توجد فصول حتى الآن</p>
                @endif
            </div>

            <!-- Vision & Mission -->
            @if($businessPlan->vision || $businessPlan->mission)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @if($businessPlan->vision)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        الرؤية
                    </h3>
                    <p class="text-gray-700">{{ $businessPlan->vision }}</p>
                </div>
                @endif

                @if($businessPlan->mission)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        المهمة
                    </h3>
                    <p class="text-gray-700">{{ $businessPlan->mission }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
