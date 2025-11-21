<x-layouts.app>
    <x-slot name="title">لوحة التحكم</x-slot>
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">لوحة التحكم</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">مرحباً {{ auth()->user()->name }}، إليك نظرة عامة على خطط عملك</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Plans -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">إجمالي الخطط</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total_plans'] }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Draft Plans -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">مسودات</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['draft_plans'] }}</p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full">
                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">قيد التنفيذ</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['in_progress_plans'] }}</p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900 p-3 rounded-full">
                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">مكتملة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['completed_plans'] }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Completion Progress -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">متوسط نسبة الإكمال</h3>
            <div class="flex items-center justify-center">
                <div class="relative w-48 h-48">
                    <svg class="w-full h-full" viewBox="0 0 100 100">
                        <circle class="text-gray-200 dark:text-gray-700 stroke-current" stroke-width="10" cx="50" cy="50" r="40" fill="transparent"></circle>
                        <circle class="text-blue-600 dark:text-blue-400 progress-ring stroke-current" stroke-width="10" stroke-linecap="round" cx="50" cy="50" r="40" fill="transparent"
                                stroke-dasharray="{{ 251.2 * $stats['average_completion'] / 100 }} 251.2"
                                transform="rotate(-90 50 50)"></circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['average_completion'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plans by Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">توزيع الخطط حسب الحالة</h3>
            <div class="space-y-3">
                @foreach($plansByStatus as $status => $count)
                <div class="flex items-center justify-between">
                    <span class="text-gray-700 dark:text-gray-300">
                        @switch($status)
                            @case('draft') مسودة @break
                            @case('in_progress') قيد التنفيذ @break
                            @case('review') مراجعة @break
                            @case('completed') مكتمل @break
                            @case('archived') مؤرشف @break
                            @default {{ $status }}
                        @endswitch
                    </span>
                    <div class="flex items-center">
                        <span class="text-gray-900 dark:text-white font-semibold mr-2">{{ $count }}</span>
                        <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full" style="width: {{ ($count / $stats['total_plans']) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Plans -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">آخر الخطط</h3>
                <a href="{{ route('business-plans.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">عرض الكل</a>
            </div>
        </div>
        <div class="p-6">
            @if($recentPlans->count() > 0)
            <div class="space-y-4">
                @foreach($recentPlans as $plan)
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $plan->title }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $plan->company_name }}</p>
                        <div class="flex items-center mt-2 space-x-4 space-x-reverse">
                            <span class="text-xs px-2 py-1 rounded-full
                                @if($plan->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($plan->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200
                                @endif">
                                @switch($plan->status)
                                    @case('draft') مسودة @break
                                    @case('in_progress') قيد التنفيذ @break
                                    @case('review') مراجعة @break
                                    @case('completed') مكتمل @break
                                    @default {{ $plan->status }}
                                @endswitch
                            </span>
                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $plan->completion_percentage }}% مكتمل</span>
                        </div>
                    </div>
                    <a href="{{ route('business-plans.show', $plan) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-600 dark:text-gray-400 mb-4">لم تقم بإنشاء أي خطة عمل بعد</p>
                <a href="{{ route('wizard.start') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    إنشاء خطة جديدة
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- New Features Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 mb-8">
        <div class="flex items-center mb-4">
            <svg class="w-8 h-8 text-white ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <h3 class="text-2xl font-bold text-white">ميزات جديدة متاحة الآن!</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Version History -->
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 hover:bg-opacity-30 transition">
                <div class="flex items-center mb-2">
                    <span class="text-3xl ml-2">📜</span>
                    <h4 class="text-white font-bold text-lg">سجل الإصدارات</h4>
                </div>
                <p class="text-white text-opacity-90 text-sm mb-3">احفظ نسخ متعددة من خطتك واستعدها في أي وقت</p>
                <span class="inline-block bg-white bg-opacity-20 text-white text-xs px-3 py-1 rounded-full">متاح في كل خطة</span>
            </div>

            <!-- Translation -->
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 hover:bg-opacity-30 transition">
                <div class="flex items-center mb-2">
                    <span class="text-3xl ml-2">🌍</span>
                    <h4 class="text-white font-bold text-lg">ترجمة تلقائية</h4>
                </div>
                <p class="text-white text-opacity-90 text-sm mb-3">ترجم خطتك إلى 10 لغات عالمية بالذكاء الاصطناعي</p>
                <span class="inline-block bg-white bg-opacity-20 text-white text-xs px-3 py-1 rounded-full">10 لغات</span>
            </div>

            <!-- Drag & Drop -->
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 hover:bg-opacity-30 transition">
                <div class="flex items-center mb-2">
                    <span class="text-3xl ml-2">🎯</span>
                    <h4 class="text-white font-bold text-lg">إعادة ترتيب سهلة</h4>
                </div>
                <p class="text-white text-opacity-90 text-sm mb-3">رتب فصول خطتك بالسحب والإفلات ببساطة</p>
                <span class="inline-block bg-white bg-opacity-20 text-white text-xs px-3 py-1 rounded-full">في محرر الفصول</span>
            </div>

            <!-- Infographic -->
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 hover:bg-opacity-30 transition">
                <div class="flex items-center mb-2">
                    <span class="text-3xl ml-2">📊</span>
                    <h4 class="text-white font-bold text-lg">إنفوجرافيك تلقائي</h4>
                </div>
                <p class="text-white text-opacity-90 text-sm mb-3">احصل على ملخص مرئي احترافي لخطة عملك</p>
                <span class="inline-block bg-white bg-opacity-20 text-white text-xs px-3 py-1 rounded-full">تصميم احترافي</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('wizard.start') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg shadow p-6 hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="flex items-center">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <div class="mr-4">
                    <h4 class="font-bold text-lg">خطة جديدة</h4>
                    <p class="text-sm text-blue-100">ابدأ خطة عمل جديدة</p>
                </div>
            </div>
        </a>

        <a href="{{ route('business-plans.index') }}" class="bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg shadow p-6 hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="flex items-center">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                <div class="mr-4">
                    <h4 class="font-bold text-lg">كل الخطط</h4>
                    <p class="text-sm text-purple-100">عرض جميع خطط العمل</p>
                </div>
            </div>
        </a>

        <a href="{{ route('export-templates.index') }}" class="bg-gradient-to-r from-orange-600 to-orange-700 text-white rounded-lg shadow p-6 hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="flex items-center">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                </svg>
                <div class="mr-4">
                    <h4 class="font-bold text-lg">قوالب التصدير</h4>
                    <p class="text-sm text-orange-100">خصص شكل صادراتك</p>
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard.analytics') }}" class="bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg shadow p-6 hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="flex items-center">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <div class="mr-4">
                    <h4 class="font-bold text-lg">الإحصائيات</h4>
                    <p class="text-sm text-green-100">عرض تحليلات مفصلة</p>
                </div>
            </div>
        </a>
    </div>
</div>
</x-layouts.app>
