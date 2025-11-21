<x-layouts.app>
    <x-slot name="title">{{ $version->version_name }} - {{ $businessPlan->title }}</x-slot>

    <div class="min-h-screen bg-gray-50 py-8" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="{{ route('business-plans.versions.index', $businessPlan) }}" class="text-gray-600 hover:text-gray-900 ml-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $version->version_name }}</h1>
                            <p class="text-gray-600 mt-1">الإصدار #{{ $version->version_number }} - {{ $version->created_at->format('Y/m/d H:i') }}</p>
                        </div>
                    </div>
                    <form action="{{ route('business-plans.versions.restore', [$businessPlan, $version]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من استعادة هذا الإصدار؟')">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            استعادة هذا الإصدار
                        </button>
                    </form>
                </div>

                @if($version->changes_summary)
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-700 mb-1">ملخص التغييرات:</h3>
                        <p class="text-gray-700">{{ $version->changes_summary }}</p>
                    </div>
                @endif

                <div class="mt-4 flex items-center text-sm text-gray-500 space-x-reverse space-x-4">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ $version->creator->name ?? 'غير معروف' }}
                    </span>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $version->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>

            <!-- Version Content -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">محتوى الإصدار</h2>

                <!-- Basic Info -->
                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-1">عنوان الخطة</h3>
                        <p class="text-gray-900">{{ $version->snapshot['title'] ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-1">اسم الشركة</h3>
                        <p class="text-gray-900">{{ $version->snapshot['company_name'] ?? 'غير محدد' }}</p>
                    </div>
                </div>

                @if(!empty($version->snapshot['description']))
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-600 mb-1">الوصف</h3>
                        <p class="text-gray-700">{{ $version->snapshot['description'] }}</p>
                    </div>
                @endif

                @if(!empty($version->snapshot['vision']))
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-600 mb-1">الرؤية</h3>
                        <p class="text-gray-700">{{ $version->snapshot['vision'] }}</p>
                    </div>
                @endif

                @if(!empty($version->snapshot['mission']))
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-600 mb-1">الرسالة</h3>
                        <p class="text-gray-700">{{ $version->snapshot['mission'] }}</p>
                    </div>
                @endif

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-1">الحالة</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ ($version->snapshot['status'] ?? '') === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ ($version->snapshot['status'] ?? '') === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ ($version->snapshot['status'] ?? '') === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}">
                            {{ $version->snapshot['status'] ?? 'غير محدد' }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-1">نسبة الإكمال</h3>
                        <p class="text-gray-900">{{ $version->snapshot['completion_percentage'] ?? 0 }}%</p>
                    </div>
                </div>
            </div>

            <!-- Chapters -->
            @if(!empty($version->snapshot['chapters']) && is_array($version->snapshot['chapters']))
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">الفصول ({{ count($version->snapshot['chapters']) }})</h2>
                    <div class="space-y-4">
                        @foreach($version->snapshot['chapters'] as $chapter)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $chapter['title'] ?? 'بدون عنوان' }}</h3>
                                @if(!empty($chapter['content']))
                                    <div class="text-gray-700 leading-relaxed prose max-w-none">
                                        {{ Str::limit($chapter['content'], 300) }}
                                    </div>
                                @else
                                    <p class="text-gray-500 italic">لا يوجد محتوى</p>
                                @endif
                                <div class="mt-2 text-xs text-gray-500">
                                    الحالة: {{ $chapter['status'] ?? 'غير محدد' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireScripts
</x-layouts.app>
