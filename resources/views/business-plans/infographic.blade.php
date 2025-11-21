<x-layouts.app>
    <x-slot name="title">إنفوجرافيك - {{ $businessPlan->title }}</x-slot>

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
                            <h1 class="text-2xl font-bold text-gray-900">إنفوجرافيك الخطة</h1>
                            <p class="text-gray-600 mt-1">{{ $businessPlan->title }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ $imageUrl }}" download="infographic_{{ $businessPlan->id }}.png" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            تحميل
                        </a>
                        <button onclick="shareImage()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                            </svg>
                            مشاركة
                        </button>
                    </div>
                </div>
            </div>

            <!-- Infographic Display -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    <img src="{{ $imageUrl }}" alt="إنفوجرافيك خطة العمل" class="mx-auto max-w-full h-auto rounded-lg shadow-lg">
                </div>

                <!-- Info -->
                <div class="mt-6 bg-blue-50 border-r-4 border-blue-500 p-4 rounded">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-500 ml-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-700">
                            <p class="font-semibold mb-1">معلومات:</p>
                            <ul class="list-disc mr-5 space-y-1">
                                <li>الإنفوجرافيك يحتوي على ملخص مرئي لخطة عملك</li>
                                <li>يمكنك تحميله ومشاركته على وسائل التواصل الاجتماعي</li>
                                <li>الصورة بجودة عالية مناسبة للطباعة</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function shareImage() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $businessPlan->title }}',
                    text: 'إنفوجرافيك خطة العمل',
                    url: window.location.href
                }).then(() => {
                    console.log('Shared successfully');
                }).catch((error) => {
                    console.error('Error sharing:', error);
                    copyToClipboard();
                });
            } else {
                copyToClipboard();
            }
        }

        function copyToClipboard() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('تم نسخ الرابط إلى الحافظة');
            }).catch((error) => {
                console.error('Error copying:', error);
            });
        }
    </script>
</x-layouts.app>
