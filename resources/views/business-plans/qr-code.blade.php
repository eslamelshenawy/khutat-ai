<x-layouts.app>
    <x-slot name="title">رمز QR - {{ $businessPlan->title }}</x-slot>

    <div class="min-h-screen bg-gray-50 py-8" dir="rtl">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center mb-4">
                    <a href="{{ route('business-plans.show', $businessPlan) }}" class="text-gray-600 hover:text-gray-900 ml-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">رمز QR للخطة</h1>
                </div>
                <p class="text-gray-600 mr-10">{{ $businessPlan->title }}</p>
            </div>

            <!-- QR Code Display -->
            <div class="bg-white rounded-lg shadow-md p-8">
                <div class="text-center">
                    <div class="inline-block p-6 bg-white rounded-lg border-4 border-gray-200 shadow-lg mb-6">
                        <div class="bg-white p-4">
                            <img src="{{ $qrCodeDataUrl }}" alt="QR Code" class="mx-auto" style="width: 300px; height: 300px;">
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-3">امسح الرمز للوصول للخطة</h2>
                    <p class="text-gray-600 mb-6">
                        استخدم تطبيق الكاميرا أو تطبيق قارئ QR للوصول السريع إلى خطة العمل
                    </p>

                    <!-- Download Button -->
                    <div class="flex justify-center gap-4 mb-8">
                        <a href="{{ route('business-plans.qr-code.download', $businessPlan) }}"
                           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition flex items-center shadow-md">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            تحميل الرمز
                        </a>
                        <button onclick="printQRCode()"
                                class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition flex items-center shadow-md">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            طباعة
                        </button>
                    </div>

                    <!-- Info Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-1">مشاركة سريعة</h3>
                            <p class="text-sm text-gray-600">شارك خطتك بسهولة عبر رمز QR</p>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-1">آمن</h3>
                            <p class="text-sm text-gray-600">محمي بنظام الصلاحيات الخاص بك</p>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-4 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full mb-3">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-1">طباعة وتحميل</h3>
                            <p class="text-sm text-gray-600">اطبع الرمز أو حمله كصورة</p>
                        </div>
                    </div>

                    <!-- Usage Instructions -->
                    <div class="bg-gray-50 rounded-lg p-6 mt-6 text-right">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            كيفية الاستخدام
                        </h3>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-600 rounded-full ml-2 flex-shrink-0 font-semibold text-xs">1</span>
                                <span>افتح تطبيق الكاميرا على هاتفك الذكي</span>
                            </li>
                            <li class="flex items-start">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-600 rounded-full ml-2 flex-shrink-0 font-semibold text-xs">2</span>
                                <span>وجه الكاميرا نحو رمز QR</span>
                            </li>
                            <li class="flex items-start">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-600 rounded-full ml-2 flex-shrink-0 font-semibold text-xs">3</span>
                                <span>اضغط على الإشعار الذي يظهر للوصول إلى الخطة مباشرة</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printQRCode() {
            window.print();
        }
    </script>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .bg-white.rounded-lg.shadow-md.p-8,
            .bg-white.rounded-lg.shadow-md.p-8 * {
                visibility: visible;
            }
            .bg-white.rounded-lg.shadow-md.p-8 {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
            }
            button, a {
                display: none !important;
            }
        }
    </style>
</x-layouts.app>
