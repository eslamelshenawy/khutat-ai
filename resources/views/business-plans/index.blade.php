<x-layouts.app>
    <x-slot name="title">خطط الأعمال</x-slot>

    <!-- Main Content -->
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">خطط الأعمال</h1>
                    <p class="mt-2 text-gray-600">إدارة جميع خطط الأعمال الخاصة بك</p>
                </div>
                <a href="{{ route('wizard.start') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition flex items-center shadow-md">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    خطة جديدة
                </a>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
            @endif

            <!-- Business Plans Grid -->
            @if($businessPlans->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($businessPlans as $plan)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow border border-gray-200 overflow-hidden">
                            <div class="p-6">
                                <!-- Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <h3 class="text-xl font-bold text-gray-900 line-clamp-2 flex-1">
                                        {{ $plan->title }}
                                    </h3>
                                    <span class="mr-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        @if($plan->status === 'draft') bg-yellow-100 text-yellow-800
                                        @elseif($plan->status === 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($plan->status === 'completed') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        @if($plan->status === 'draft') مسودة
                                        @elseif($plan->status === 'in_progress') قيد التنفيذ
                                        @elseif($plan->status === 'completed') مكتمل
                                        @else {{ $plan->status }}
                                        @endif
                                    </span>
                                </div>

                                <!-- Description -->
                                @if($plan->description)
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $plan->description }}</p>
                                @endif

                                <!-- Progress Bar -->
                                <div class="mb-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-medium text-gray-700">نسبة الإنجاز</span>
                                        <span class="text-xs font-bold text-blue-600">{{ $plan->completion_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-l from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500"
                                             style="width: {{ $plan->completion_percentage }}%"></div>
                                    </div>
                                </div>

                                <!-- Meta Info -->
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-4 pb-4 border-b border-gray-200">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>{{ $plan->chapters_count }} فصل</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $plan->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="{{ route('business-plans.show', $plan) }}"
                                       class="flex-1 bg-blue-600 text-white text-center px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                        عرض
                                    </a>
                                    <a href="{{ route('chapters.edit', $plan) }}"
                                       class="flex-1 bg-green-600 text-white text-center px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                                        تحرير
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $businessPlans->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-6 text-xl font-bold text-gray-900">لا توجد خطط عمل حتى الآن</h3>
                    <p class="mt-2 text-gray-600">ابدأ بإنشاء خطة عمل جديدة الآن</p>
                    <a href="{{ route('wizard.start') }}"
                       class="mt-6 inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-md">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        إنشاء خطة عمل جديدة
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
