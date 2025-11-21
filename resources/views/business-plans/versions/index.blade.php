<x-layouts.app>
    <x-slot name="title">سجل الإصدارات - {{ $businessPlan->title }}</x-slot>

    <div class="min-h-screen bg-gray-50 py-8" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="{{ route('business-plans.show', $businessPlan) }}" class="text-gray-600 hover:text-gray-900 ml-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">سجل الإصدارات</h1>
                            <p class="text-gray-600 mt-1">{{ $businessPlan->title }}</p>
                        </div>
                    </div>
                    <button onclick="document.getElementById('createVersionModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        حفظ نسخة جديدة
                    </button>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                    <div class="flex">
                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Versions Timeline -->
            @if($versions->count() > 0)
                <div class="space-y-4">
                    @foreach($versions as $version)
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <div class="bg-blue-100 rounded-full p-2 ml-3">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">{{ $version->version_name }}</h3>
                                            <p class="text-sm text-gray-500">الإصدار #{{ $version->version_number }}</p>
                                        </div>
                                    </div>

                                    @if($version->changes_summary)
                                        <p class="text-gray-700 mb-3 mr-12">{{ $version->changes_summary }}</p>
                                    @endif

                                    <div class="flex items-center text-sm text-gray-500 mr-12 space-x-reverse space-x-4">
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

                                <div class="flex items-center space-x-reverse space-x-2">
                                    <a href="{{ route('business-plans.versions.show', [$businessPlan, $version]) }}" class="text-blue-600 hover:text-blue-800 px-3 py-2 text-sm">
                                        عرض
                                    </a>
                                    <form action="{{ route('business-plans.versions.restore', [$businessPlan, $version]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من استعادة هذا الإصدار؟ سيتم حفظ نسخة احتياطية من الحالة الحالية.')">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 px-3 py-2 text-sm">
                                            استعادة
                                        </button>
                                    </form>
                                    <form action="{{ route('business-plans.versions.destroy', [$businessPlan, $version]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإصدار؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 px-3 py-2 text-sm">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $versions->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد إصدارات محفوظة</h3>
                    <p class="text-gray-500 mb-4">قم بحفظ نسخة جديدة لبدء تتبع التغييرات</p>
                    <button onclick="document.getElementById('createVersionModal').classList.remove('hidden')" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        حفظ نسخة الآن
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Version Modal -->
    <div id="createVersionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">حفظ نسخة جديدة</h3>
                <form action="{{ route('business-plans.versions.store', $businessPlan) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="version_name" class="block text-sm font-medium text-gray-700 mb-2">اسم الإصدار (اختياري)</label>
                        <input type="text" name="version_name" id="version_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="مثال: النسخة النهائية">
                    </div>
                    <div class="mb-4">
                        <label for="changes_summary" class="block text-sm font-medium text-gray-700 mb-2">ملخص التغييرات (اختياري)</label>
                        <textarea name="changes_summary" id="changes_summary" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="اذكر أهم التغييرات في هذا الإصدار..."></textarea>
                    </div>
                    <div class="flex items-center justify-end space-x-reverse space-x-3">
                        <button type="button" onclick="document.getElementById('createVersionModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            إلغاء
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireScripts
</x-layouts.app>
