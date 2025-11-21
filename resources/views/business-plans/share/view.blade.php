<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $businessPlan->title }} - خطة عمل</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=tajawal:400,500,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Tajawal', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-2 ml-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">خطة عمل مشتركة</h1>
                        <p class="text-sm text-gray-500">عرض فقط</p>
                    </div>
                </div>
                <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    طباعة
                </button>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Business Plan Header -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            @if($businessPlan->company_logo)
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('storage/' . $businessPlan->company_logo) }}" alt="{{ $businessPlan->company_name }}" class="h-20">
                </div>
            @endif

            <h1 class="text-4xl font-bold text-gray-900 text-center mb-4">{{ $businessPlan->title }}</h1>

            @if($businessPlan->company_name)
                <p class="text-xl text-gray-600 text-center mb-6">{{ $businessPlan->company_name }}</p>
            @endif

            @if($businessPlan->description)
                <p class="text-gray-700 text-center max-w-3xl mx-auto">{{ $businessPlan->description }}</p>
            @endif

            <div class="flex justify-center items-center space-x-reverse space-x-6 mt-6 text-sm text-gray-500">
                @if($businessPlan->project_type)
                    <span class="flex items-center">
                        <svg class="w-5 h-5 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        {{ $businessPlan->project_type }}
                    </span>
                @endif
                @if($businessPlan->industry_type)
                    <span class="flex items-center">
                        <svg class="w-5 h-5 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        {{ $businessPlan->industry_type }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Vision & Mission -->
        @if($businessPlan->vision || $businessPlan->mission)
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                @if($businessPlan->vision)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-3 flex items-center">
                            <svg class="w-6 h-6 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            الرؤية
                        </h2>
                        <p class="text-gray-700 leading-relaxed">{{ $businessPlan->vision }}</p>
                    </div>
                @endif

                @if($businessPlan->mission)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-3 flex items-center">
                            <svg class="w-6 h-6 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                            الرسالة
                        </h2>
                        <p class="text-gray-700 leading-relaxed">{{ $businessPlan->mission }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Chapters -->
        @if($businessPlan->chapters && $businessPlan->chapters->count() > 0)
            <div class="space-y-6">
                @foreach($businessPlan->chapters as $chapter)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ $chapter->title }}
                        </h2>

                        @if($chapter->description)
                            <p class="text-gray-600 mb-4">{{ $chapter->description }}</p>
                        @endif

                        @if($chapter->content)
                            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                                {!! nl2br(e($chapter->content)) !!}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- AI Score -->
        @if($businessPlan->ai_score)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-md p-6 mt-6">
                <div class="flex items-start">
                    <div class="bg-blue-100 rounded-full p-3 ml-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">تقييم الذكاء الاصطناعي</h3>
                        <div class="flex items-center mb-3">
                            <span class="text-3xl font-bold text-blue-600 ml-2">{{ $businessPlan->ai_score }}</span>
                            <span class="text-gray-600">/ 100</span>
                        </div>
                        @if($businessPlan->ai_feedback)
                            <p class="text-gray-700">{{ $businessPlan->ai_feedback }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Footer Info -->
        <div class="bg-gray-100 rounded-lg p-6 mt-6 no-print">
            <div class="flex items-center justify-center text-sm text-gray-600">
                <svg class="w-5 h-5 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                هذه خطة عمل مشتركة معك للعرض فقط. لا يمكنك إجراء تعديلات على هذا المحتوى.
            </div>
        </div>
    </div>

    <!-- Powered By (print only) -->
    <div class="hidden print:block text-center py-6 text-sm text-gray-500">
        <p>تم إنشاؤها باستخدام معالج خطط الأعمال - al-investor.com</p>
    </div>
</body>
</html>
