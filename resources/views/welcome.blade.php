<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>معالج خطط الأعمال - أنشئ خطة عملك باحترافية</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- خط تجوال العربي -->
    <link href="https://fonts.bunny.net/css?family=tajawal:400,500,700&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }
    </style>
</head>
<body class="antialiased bg-gradient-to-br from-blue-50 to-indigo-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center group">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 p-2 rounded-xl shadow-lg group-hover:shadow-xl transition-all">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="mr-3 text-2xl font-bold bg-gradient-to-l from-blue-600 to-indigo-600 bg-clip-text text-transparent">معالج خطط الأعمال</span>
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('business-plans.index') }}" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-blue-50">
                            خططي
                        </a>
                        <a href="{{ url('/admin') }}" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-blue-50">
                            لوحة التحكم
                        </a>
                        <a href="{{ route('wizard.start') }}" class="bg-gradient-to-l from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg hover:shadow-xl transition-all">
                            إنشاء خطة جديدة
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-blue-50">
                            تسجيل الدخول
                        </a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-l from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg hover:shadow-xl transition-all">
                            إنشاء حساب
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-20 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                أنشئ خطة عملك
                <span class="bg-gradient-to-l from-blue-600 to-indigo-600 bg-clip-text text-transparent">باحترافية</span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                منصة متكاملة لإنشاء وإدارة خطط الأعمال باستخدام الذكاء الاصطناعي. ابدأ مشروعك الآن بخطة احترافية ومفصلة.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @auth
                    <a href="{{ route('wizard.start') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-l from-blue-600 to-indigo-600 text-white text-lg font-bold rounded-xl shadow-2xl hover:shadow-3xl hover:from-blue-700 hover:to-indigo-700 transition-all transform hover:scale-105">
                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        ابدأ الآن مجاناً
                    </a>
                    <a href="{{ route('business-plans.index') }}" class="inline-flex items-center px-8 py-4 bg-white text-gray-700 text-lg font-semibold rounded-xl shadow-lg hover:shadow-xl border-2 border-gray-200 hover:border-blue-300 transition-all">
                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        خططي الحالية
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-l from-blue-600 to-indigo-600 text-white text-lg font-bold rounded-xl shadow-2xl hover:shadow-3xl hover:from-blue-700 hover:to-indigo-700 transition-all transform hover:scale-105">
                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        ابدأ الآن مجاناً
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-4 bg-white text-gray-700 text-lg font-semibold rounded-xl shadow-lg hover:shadow-xl border-2 border-gray-200 hover:border-blue-300 transition-all">
                        تسجيل الدخول
                    </a>
                @endauth
            </div>

            <!-- Stats -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <div class="text-4xl font-bold bg-gradient-to-l from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">100%</div>
                    <div class="text-gray-600 font-medium">مجاني تماماً</div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <div class="text-4xl font-bold bg-gradient-to-l from-green-600 to-teal-600 bg-clip-text text-transparent mb-2">AI</div>
                    <div class="text-gray-600 font-medium">ذكاء اصطناعي متقدم</div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <div class="text-4xl font-bold bg-gradient-to-l from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">24/7</div>
                    <div class="text-gray-600 font-medium">متاح دائماً</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">المميزات الرئيسية</h2>
                <p class="text-xl text-gray-600">كل ما تحتاجه لإنشاء خطة عمل احترافية</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl border border-blue-100 hover:shadow-xl transition-all">
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-600 w-14 h-14 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">ذكاء اصطناعي متطور</h3>
                    <p class="text-gray-600 leading-relaxed">استخدم تقنية Ollama المفتوحة المصدر لتوليد محتوى احترافي لخطة عملك تلقائياً</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-green-50 to-teal-50 p-8 rounded-2xl border border-green-100 hover:shadow-xl transition-all">
                    <div class="bg-gradient-to-br from-green-600 to-teal-600 w-14 h-14 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">معالج تفاعلي</h3>
                    <p class="text-gray-600 leading-relaxed">واجهة سهلة وبسيطة ترشدك خطوة بخطوة لإنشاء خطة عمل متكاملة</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-8 rounded-2xl border border-purple-100 hover:shadow-xl transition-all">
                    <div class="bg-gradient-to-br from-purple-600 to-pink-600 w-14 h-14 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">قوالب جاهزة</h3>
                    <p class="text-gray-600 leading-relaxed">اختر من بين قوالب متنوعة لمختلف أنواع المشاريع والصناعات</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-8 rounded-2xl border border-yellow-100 hover:shadow-xl transition-all">
                    <div class="bg-gradient-to-br from-yellow-600 to-orange-600 w-14 h-14 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">تصدير متعدد</h3>
                    <p class="text-gray-600 leading-relaxed">صدّر خطتك بصيغ PDF و DOCX بتنسيق احترافي</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gradient-to-br from-red-50 to-rose-50 p-8 rounded-2xl border border-red-100 hover:shadow-xl transition-all">
                    <div class="bg-gradient-to-br from-red-600 to-rose-600 w-14 h-14 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">تتبع التقدم</h3>
                    <p class="text-gray-600 leading-relaxed">راقب نسبة إنجاز خطتك وتقدمك في كل فصل</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gradient-to-br from-cyan-50 to-sky-50 p-8 rounded-2xl border border-cyan-100 hover:shadow-xl transition-all">
                    <div class="bg-gradient-to-br from-cyan-600 to-sky-600 w-14 h-14 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">آمن ومحمي</h3>
                    <p class="text-gray-600 leading-relaxed">بياناتك محمية بأعلى معايير الأمان والخصوصية</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-l from-blue-600 to-indigo-600">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">جاهز لبدء مشروعك؟</h2>
            <p class="text-xl text-blue-100 mb-8 leading-relaxed">ابدأ الآن في إنشاء خطة عملك الاحترافية مجاناً</p>
            @auth
                <a href="{{ route('wizard.start') }}" class="inline-flex items-center px-10 py-4 bg-white text-blue-600 text-lg font-bold rounded-xl shadow-2xl hover:shadow-3xl hover:bg-blue-50 transition-all transform hover:scale-105">
                    <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    ابدأ الآن
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center px-10 py-4 bg-white text-blue-600 text-lg font-bold rounded-xl shadow-2xl hover:shadow-3xl hover:bg-blue-50 transition-all transform hover:scale-105">
                    <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    إنشاء حساب مجاني
                </a>
            @endauth
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">معالج خطط الأعمال</h3>
                    <p class="text-gray-400 leading-relaxed">منصة متكاملة لإنشاء وإدارة خطط الأعمال باستخدام الذكاء الاصطناعي</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">روابط سريعة</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ url('/') }}" class="hover:text-white transition-colors">الرئيسية</a></li>
                        @auth
                            <li><a href="{{ route('business-plans.index') }}" class="hover:text-white transition-colors">خططي</a></li>
                            <li><a href="{{ url('/admin') }}" class="hover:text-white transition-colors">لوحة التحكم</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">تسجيل الدخول</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">إنشاء حساب</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">تواصل معنا</h3>
                    <p class="text-gray-400">لأي استفسارات أو دعم فني</p>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} معالج خطط الأعمال. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>
</body>
</html>
