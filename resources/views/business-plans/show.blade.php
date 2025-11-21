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
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                <div class="py-1">
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 border-b">تصدير</div>
                                    <a href="{{ route('business-plans.export', [$businessPlan, 'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📄 تصدير PDF
                                    </a>
                                    <a href="{{ route('business-plans.export', [$businessPlan, 'docx']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📝 تصدير Word
                                    </a>
                                    <a href="{{ route('business-plans.export', [$businessPlan, 'xlsx']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📊 تصدير Excel
                                    </a>
                                    <a href="{{ route('business-plans.export-powerpoint', $businessPlan) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📽️ تصدير PowerPoint
                                    </a>
                                    <a href="{{ route('business-plans.infographic', $businessPlan) }}" target="_blank" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📊 توليد إنفوجرافيك
                                    </a>
                                    <a href="{{ route('business-plans.qr-code', $businessPlan) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📱 رمز QR
                                    </a>

                                    <div class="border-t my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500">ذكاء اصطناعي</div>
                                    <form action="{{ route('business-plans.analyze', $businessPlan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            🤖 تحليل الخطة بـ AI
                                        </button>
                                    </form>
                                    <form action="{{ route('business-plans.recommendations', $businessPlan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            💡 توليد التوصيات
                                        </button>
                                    </form>

                                    <div class="border-t my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500">إدارة</div>
                                    <a href="{{ route('financial.index', $businessPlan) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        💰 البيانات المالية
                                    </a>
                                    <a href="{{ route('tasks.index', $businessPlan) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        ✅ إدارة المهام
                                    </a>
                                    <a href="{{ route('business-plans.translate', $businessPlan) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🌐 ترجمة الخطة
                                    </a>

                                    <div class="border-t my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500">مشاركة</div>
                                    <a href="{{ route('business-plans.share.create', $businessPlan) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🔗 إنشاء رابط مشاركة
                                    </a>

                                    <div class="border-t my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500">الإصدارات</div>
                                    <a href="{{ route('business-plans.versions.index', $businessPlan) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📜 سجل الإصدارات
                                    </a>

                                    <div class="border-t my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500">اللغة</div>
                                    <a href="{{ route('business-plans.translate', $businessPlan) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🌍 ترجمة الخطة
                                    </a>

                                    <div class="border-t my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500">إجراءات</div>
                                    <form action="{{ route('business-plans.duplicate', $businessPlan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            📋 نسخ الخطة
                                        </button>
                                    </form>
                                    <form action="{{ route('business-plans.destroy', $businessPlan) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="block w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                            🗑️ حذف الخطة
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

            <!-- AI Analysis & Recommendations -->
            @if($businessPlan->ai_score || $businessPlan->aiRecommendations->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @if($businessPlan->ai_score)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        تحليل الذكاء الاصطناعي
                    </h3>
                    <div class="flex items-center mb-4">
                        <div class="relative w-24 h-24">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                <circle class="text-gray-200 stroke-current" stroke-width="10" cx="50" cy="50" r="40" fill="transparent"></circle>
                                <circle class="text-purple-600 stroke-current" stroke-width="10" stroke-linecap="round" cx="50" cy="50" r="40" fill="transparent"
                                    stroke-dasharray="{{ 251.2 * $businessPlan->ai_score / 100 }} 251.2"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-2xl font-bold text-gray-900">{{ $businessPlan->ai_score }}</span>
                            </div>
                        </div>
                        <div class="mr-4">
                            <p class="text-sm text-gray-500">درجة التقييم</p>
                            <p class="text-lg font-semibold
                                @if($businessPlan->ai_score >= 80) text-green-600
                                @elseif($businessPlan->ai_score >= 60) text-yellow-600
                                @else text-red-600
                                @endif">
                                @if($businessPlan->ai_score >= 80) ممتازة
                                @elseif($businessPlan->ai_score >= 60) جيدة
                                @else تحتاج تحسين
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($businessPlan->ai_feedback)
                    <div class="text-sm text-gray-700 prose prose-sm max-w-none">
                        {!! nl2br(e(Str::limit($businessPlan->ai_feedback, 300))) !!}
                    </div>
                    @endif
                </div>
                @endif

                @if($businessPlan->aiRecommendations->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        التوصيات
                    </h3>
                    <div class="space-y-3">
                        @foreach($businessPlan->aiRecommendations->take(3) as $recommendation)
                        <div class="border-r-4 pr-3
                            @if($recommendation->priority === 'high') border-red-500
                            @elseif($recommendation->priority === 'medium') border-yellow-500
                            @else border-blue-500
                            @endif">
                            <p class="font-semibold text-gray-900 text-sm">{{ $recommendation->title }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ Str::limit($recommendation->description, 100) }}</p>
                        </div>
                        @endforeach
                    </div>
                    @if($businessPlan->aiRecommendations->count() > 3)
                    <p class="text-sm text-blue-600 mt-4">+ {{ $businessPlan->aiRecommendations->count() - 3 }} توصية أخرى</p>
                    @endif
                </div>
                @endif
            </div>
            @endif

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

            <!-- Images & Logo Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    الصور والشعار
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Logo Upload -->
                    <div>
                        <h4 class="font-bold text-gray-900 mb-3">شعار الشركة</h4>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            @if($businessPlan->getFirstMedia('logo'))
                                <img src="{{ $businessPlan->getFirstMedia('logo')->getUrl() }}"
                                     class="mx-auto mb-3 max-h-32 rounded">
                                <form action="{{ route('business-plans.delete-image', [$businessPlan, $businessPlan->getFirstMedia('logo')->id]) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">حذف الشعار</button>
                                </form>
                            @else
                                <form action="{{ route('business-plans.upload-logo', $businessPlan) }}"
                                      method="POST" enctype="multipart/form-data" id="logoForm">
                                    @csrf
                                    <input type="file" name="logo" accept="image/*" id="logoInput" class="hidden">
                                    <label for="logoInput" class="cursor-pointer">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="text-gray-600">اضغط لرفع الشعار</p>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF (Max 5MB)</p>
                                    </label>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Images Upload -->
                    <div>
                        <h4 class="font-bold text-gray-900 mb-3">صور إضافية</h4>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <form action="{{ route('business-plans.upload-image', $businessPlan) }}"
                                  method="POST" enctype="multipart/form-data" id="imageForm">
                                @csrf
                                <input type="file" name="image" accept="image/*" id="imageInput" class="hidden">
                                <label for="imageInput" class="cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-gray-600">اضغط لرفع صورة</p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF (Max 5MB)</p>
                                </label>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Uploaded Images Gallery -->
                @if($businessPlan->getMedia('images')->count() > 0)
                <div>
                    <h4 class="font-bold text-gray-900 mb-3">الصور المرفوعة</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($businessPlan->getMedia('images') as $image)
                        <div class="relative group">
                            <img src="{{ $image->getUrl() }}" class="w-full h-32 object-cover rounded-lg">
                            <form action="{{ route('business-plans.delete-image', [$businessPlan, $image->id]) }}"
                                  method="POST" class="absolute top-2 right-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-600 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Comments Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    التعليقات والنقاش
                </h3>

                <!-- Add Comment Form -->
                <form action="{{ route('comments.store', $businessPlan) }}" method="POST" class="mb-6">
                    @csrf
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-600 font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <textarea name="content" rows="3" required
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="أضف تعليقك هنا..."></textarea>
                            <button type="submit" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                إضافة تعليق
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Comments List -->
                <div class="space-y-4">
                    @forelse($businessPlan->comments as $comment)
                    <div class="border-r-4 {{ $comment->is_resolved ? 'border-green-500' : 'border-blue-500' }} bg-gray-50 p-4 rounded">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-600 font-bold">{{ substr($comment->user->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <span class="font-bold text-gray-900">{{ $comment->user->name }}</span>
                                        <span class="text-sm text-gray-500 mr-2">{{ $comment->created_at->diffForHumans() }}</span>
                                        @if($comment->is_resolved)
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded mr-2">محلول</span>
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        <form action="{{ route('comments.resolve', [$businessPlan, $comment]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-sm {{ $comment->is_resolved ? 'text-gray-600' : 'text-green-600' }} hover:underline">
                                                {{ $comment->is_resolved ? 'إلغاء الحل' : 'وضع علامة محلول' }}
                                            </button>
                                        </form>
                                        @can('delete', $comment)
                                        <form action="{{ route('comments.destroy', [$businessPlan, $comment]) }}" method="POST" class="inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا التعليق؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:underline">حذف</button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                                <p class="text-gray-700">{{ $comment->content }}</p>

                                <!-- Replies -->
                                @if($comment->replies->count() > 0)
                                <div class="mr-6 mt-3 space-y-3">
                                    @foreach($comment->replies as $reply)
                                    <div class="bg-white p-3 rounded border border-gray-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <span class="font-bold text-gray-900">{{ $reply->user->name }}</span>
                                                <span class="text-sm text-gray-500 mr-2">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            @can('delete', $reply)
                                            <form action="{{ route('comments.destroy', [$businessPlan, $reply]) }}" method="POST"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الرد؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-600 hover:underline">حذف</button>
                                            </form>
                                            @endcan
                                        </div>
                                        <p class="text-gray-700">{{ $reply->content }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                <!-- Reply Form -->
                                <form action="{{ route('comments.store', $businessPlan) }}" method="POST" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <div class="flex gap-2">
                                        <input type="text" name="content" required
                                               class="flex-1 border border-gray-300 rounded-lg px-3 py-1 text-sm focus:ring-2 focus:ring-blue-500"
                                               placeholder="اكتب رداً...">
                                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                            رد
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-lg">لا توجد تعليقات بعد</p>
                        <p class="text-sm mt-2">كن أول من يعلق على هذه الخطة</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-submit logo form on file select
        document.getElementById('logoInput')?.addEventListener('change', function() {
            if (this.files[0]) {
                document.getElementById('logoForm').submit();
            }
        });

        // Auto-submit image form on file select
        document.getElementById('imageInput')?.addEventListener('change', function() {
            if (this.files[0]) {
                document.getElementById('imageForm').submit();
            }
        });
    </script>
    @endpush
</x-layouts.app>
