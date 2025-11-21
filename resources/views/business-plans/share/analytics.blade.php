<x-layouts.app>
    <x-slot name="title">تحليلات المشاركة - {{ $share->businessPlan->title }}</x-slot>

    <div class="min-h-screen bg-gray-50 py-8" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="{{ route('business-plans.share.create', $share->businessPlan) }}" class="text-gray-600 hover:text-gray-900 ml-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">تحليلات رابط المشاركة</h1>
                            <p class="text-gray-600 mt-1">{{ $share->businessPlan->title }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $share->type === 'public' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $share->type === 'public' ? 'عام' : 'خاص' }}
                    </span>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Views -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3 ml-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">إجمالي المشاهدات</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $share->view_count }}</p>
                        </div>
                    </div>
                </div>

                <!-- Created Date -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3 ml-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">تاريخ الإنشاء</p>
                            <p class="text-lg font-bold text-gray-900">{{ $share->created_at->format('Y/m/d') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Last Viewed -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="bg-purple-100 rounded-full p-3 ml-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">آخر مشاهدة</p>
                            <p class="text-lg font-bold text-gray-900">
                                @if($share->last_viewed_at)
                                    {{ $share->last_viewed_at->diffForHumans() }}
                                @else
                                    لم يتم المشاهدة بعد
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Expiration -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="bg-{{ $share->isExpired() ? 'red' : 'yellow' }}-100 rounded-full p-3 ml-4">
                            <svg class="w-6 h-6 text-{{ $share->isExpired() ? 'red' : 'yellow' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">حالة الرابط</p>
                            <p class="text-lg font-bold text-gray-900">
                                @if($share->expires_at)
                                    @if($share->isExpired())
                                        منتهي
                                    @else
                                        ينتهي {{ $share->expires_at->diffForHumans() }}
                                    @endif
                                @else
                                    بدون انتهاء
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Share Link -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-3">رابط المشاركة</h2>
                <div class="flex items-center">
                    <input type="text" value="{{ $share->getShareUrl() }}" readonly class="flex-1 px-4 py-2 border border-gray-300 rounded-r-lg bg-gray-50">
                    <button onclick="copyLink()" class="bg-blue-600 text-white px-6 py-2 rounded-l-lg hover:bg-blue-700 transition">
                        نسخ
                    </button>
                </div>
            </div>

            <!-- Views Chart -->
            @if($views->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">المشاهدات حسب التاريخ (آخر 30 يوم)</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد المشاهدات</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الرسم البياني</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($views as $view)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($view->date)->format('Y/m/d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                            {{ $view->count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="w-full bg-gray-200 rounded-full h-4">
                                                <div class="bg-blue-600 h-4 rounded-full" style="width: {{ ($view->count / $views->max('count')) * 100 }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-12 mb-6 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد مشاهدات بعد</h3>
                    <p class="text-gray-500">عندما يقوم شخص ما بزيارة رابط المشاركة، ستظهر التحليلات هنا.</p>
                </div>
            @endif

            <!-- Top Referrers -->
            @if($topReferrers->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">أهم مصادر الزيارات</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المصدر</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد الزيارات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topReferrers as $referrer)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $referrer->referer ?? 'مباشر' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                            {{ $referrer->count }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        function copyLink() {
            const input = document.querySelector('input[type="text"]');
            input.select();
            document.execCommand('copy');
            alert('تم نسخ الرابط إلى الحافظة!');
        }
    </script>

    @livewireScripts
</x-layouts.app>
