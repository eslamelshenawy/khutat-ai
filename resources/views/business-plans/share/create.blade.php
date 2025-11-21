<x-layouts.app>
    <x-slot name="title">إنشاء رابط مشاركة - {{ $businessPlan->title }}</x-slot>

    <div class="min-h-screen bg-gray-50 py-8" dir="rtl">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center mb-4">
                    <a href="{{ route('business-plans.show', $businessPlan) }}" class="text-gray-600 hover:text-gray-900 ml-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">إنشاء رابط مشاركة</h1>
                </div>
                <p class="text-gray-600 mr-10">{{ $businessPlan->title }}</p>
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
                    @if(session('share_link'))
                        <div class="mt-4 p-3 bg-white rounded border border-green-300">
                            <p class="text-sm font-semibold mb-2">رابط المشاركة:</p>
                            <div class="flex items-center">
                                <input type="text" id="shareLink" value="{{ session('share_link') }}" readonly class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg bg-gray-50 text-sm">
                                <button onclick="copyToClipboard()" class="bg-blue-600 text-white px-4 py-2 rounded-l-lg hover:bg-blue-700 transition">
                                    نسخ
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Share Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="{{ route('business-plans.share.store', $businessPlan) }}" method="POST">
                    @csrf

                    <!-- Share Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع المشاركة</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="type" value="public" checked class="ml-3">
                                <div>
                                    <div class="font-medium">عام</div>
                                    <div class="text-sm text-gray-500">يمكن لأي شخص لديه الرابط الوصول</div>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="type" value="private" class="ml-3" id="privateType">
                                <div>
                                    <div class="font-medium">خاص</div>
                                    <div class="text-sm text-gray-500">يتطلب كلمة مرور للوصول</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Password (shown when private is selected) -->
                    <div id="passwordField" class="mb-6 hidden">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                        <input type="password" name="password" id="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="أدخل كلمة مرور للمشاركة">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permission Level -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">صلاحية الوصول</label>
                        <select name="permission" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="view">عرض فقط</option>
                            <option value="comment">عرض + تعليق</option>
                            <option value="edit">عرض + تعليق + تحرير</option>
                        </select>
                        @error('permission')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Expiration -->
                    <div class="mb-6">
                        <label for="expires_in_days" class="block text-sm font-medium text-gray-700 mb-2">
                            تاريخ انتهاء الصلاحية (اختياري)
                        </label>
                        <select name="expires_in_days" id="expires_in_days" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">بدون تاريخ انتهاء</option>
                            <option value="1">يوم واحد</option>
                            <option value="7">7 أيام</option>
                            <option value="30">30 يوم</option>
                            <option value="90">90 يوم</option>
                            <option value="365">سنة واحدة</option>
                        </select>
                        @error('expires_in_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-reverse space-x-3">
                        <a href="{{ route('business-plans.show', $businessPlan) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            إلغاء
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            إنشاء رابط المشاركة
                        </button>
                    </div>
                </form>
            </div>

            <!-- Active Shares List -->
            @if($businessPlan->activeShares->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">روابط المشاركة النشطة</h2>
                    <div class="space-y-4">
                        @foreach($businessPlan->activeShares as $share)
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $share->type === 'public' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $share->type === 'public' ? 'عام' : 'خاص' }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                            {{ $share->permission === 'view' ? 'عرض فقط' : ($share->permission === 'comment' ? 'عرض + تعليق' : 'عرض + تعليق + تحرير') }}
                                        </span>
                                    </div>
                                    <form action="{{ route('business-plans.share.deactivate', [$businessPlan, $share]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من تعطيل هذا الرابط؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                            تعطيل
                                        </button>
                                    </form>
                                </div>
                                <div class="flex items-center mt-3">
                                    <input type="text" value="{{ $share->getShareUrl() }}" readonly class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg bg-gray-50 text-sm">
                                    <button onclick="copyLink('{{ $share->getShareUrl() }}')" class="bg-gray-600 text-white px-4 py-2 rounded-l-lg hover:bg-gray-700 transition text-sm">
                                        نسخ
                                    </button>
                                </div>
                                <div class="flex items-center text-xs text-gray-500 mt-2 space-x-reverse space-x-4">
                                    <span>المشاهدات: {{ $share->view_count }}</span>
                                    @if($share->expires_at)
                                        <span>ينتهي في: {{ $share->expires_at->diffForHumans() }}</span>
                                    @endif
                                    <a href="{{ route('business-plans.share.analytics', [$businessPlan, $share]) }}" class="text-blue-600 hover:text-blue-800">
                                        عرض التحليلات
                                    </a>
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

    <script>
        // Show/hide password field based on share type
        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const passwordField = document.getElementById('passwordField');
                if (this.value === 'private') {
                    passwordField.classList.remove('hidden');
                    document.getElementById('password').required = true;
                } else {
                    passwordField.classList.add('hidden');
                    document.getElementById('password').required = false;
                }
            });
        });

        // Copy to clipboard function
        function copyToClipboard() {
            const input = document.getElementById('shareLink');
            input.select();
            document.execCommand('copy');
            alert('تم نسخ الرابط إلى الحافظة!');
        }

        function copyLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                alert('تم نسخ الرابط إلى الحافظة!');
            });
        }
    </script>

    @livewireScripts
</x-layouts.app>
