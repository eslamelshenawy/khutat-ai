<x-layouts.app>
    <x-slot name="title">تحليلات متقدمة</x-slot>

<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">تحليلات متقدمة</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">رؤى شاملة عن أداءك واستخدام النظام</p>
            </div>
            <a href="{{ route('dashboard') }}" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                ← العودة للوحة التحكم
            </a>
        </div>
    </div>

    <!-- AI Usage Statistics -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">إحصائيات استخدام الذكاء الاصطناعي</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg p-6">
                <p class="text-sm text-blue-600 dark:text-blue-300 font-medium">إجمالي الأجيال</p>
                <p class="text-3xl font-bold text-blue-900 dark:text-blue-100 mt-2">{{ number_format($aiUsageStats['total_generations']) }}</p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg p-6">
                <p class="text-sm text-green-600 dark:text-green-300 font-medium">أجيال ناجحة</p>
                <p class="text-3xl font-bold text-green-900 dark:text-green-100 mt-2">{{ number_format($aiUsageStats['successful_generations']) }}</p>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900 dark:to-red-800 rounded-lg p-6">
                <p class="text-sm text-red-600 dark:text-red-300 font-medium">أجيال فاشلة</p>
                <p class="text-3xl font-bold text-red-900 dark:text-red-100 mt-2">{{ number_format($aiUsageStats['failed_generations']) }}</p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg p-6">
                <p class="text-sm text-purple-600 dark:text-purple-300 font-medium">إجمالي الرموز</p>
                <p class="text-3xl font-bold text-purple-900 dark:text-purple-100 mt-2">{{ number_format($aiUsageStats['total_tokens_used']) }}</p>
            </div>
        </div>
    </div>

    <!-- Monthly Plans Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">الخطط الشهرية</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300">الشهر</th>
                        <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300">عدد الخطط</th>
                        <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300">الرسم البياني</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $maxCount = $monthlyPlans->max('count') ?? 1;
                    @endphp
                    @foreach($monthlyPlans as $plan)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="py-3 px-4 text-gray-900 dark:text-white">{{ $plan->month }}</td>
                        <td class="py-3 px-4 text-gray-900 dark:text-white font-bold">{{ $plan->count }}</td>
                        <td class="py-3 px-4">
                            <div class="bg-gray-200 dark:bg-gray-600 rounded-full h-4 overflow-hidden">
                                <div class="bg-blue-600 dark:bg-blue-400 h-full rounded-full"
                                     style="width: {{ ($plan->count / $maxCount) * 100 }}%"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($monthlyPlans->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p>لا توجد بيانات شهرية متاحة</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Completion Progress -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">تقدم الإكمال</h2>
        <div class="space-y-4">
            @foreach($completionProgress as $plan)
            <div class="border dark:border-gray-700 rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $plan->title }}</h3>
                    <span class="text-sm px-3 py-1 rounded-full
                        @if($plan->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                        @elseif($plan->status === 'in_progress') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                        @endif">
                        @if($plan->status === 'completed') مكتملة
                        @elseif($plan->status === 'in_progress') قيد التنفيذ
                        @else مسودة
                        @endif
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex-1 bg-gray-200 dark:bg-gray-600 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-full rounded-full transition-all duration-500"
                             style="width: {{ $plan->completion_percentage }}%"></div>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white min-w-[60px] text-left">{{ $plan->completion_percentage }}%</span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">تم الإنشاء: {{ $plan->created_at->format('Y-m-d') }}</p>
            </div>
            @endforeach

            @if($completionProgress->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p class="text-lg font-medium">لا توجد خطط بعد</p>
                <p class="mt-2">ابدأ بإنشاء خطة عمل جديدة لرؤية التحليلات</p>
                <a href="{{ route('wizard.start') }}" class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    إنشاء خطة جديدة
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

</x-layouts.app>
