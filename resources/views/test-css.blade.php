<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار CSS</title>

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
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-4xl font-bold text-blue-600 mb-4">اختبار CSS</h1>
                <p class="text-gray-700 text-lg mb-4">إذا رأيت هذا النص بشكل صحيح مع تنسيق جيد، فإن Tailwind CSS يعمل!</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-2">بطاقة 1</h2>
                        <p>هذا نص تجريبي للبطاقة الأولى</p>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-2">بطاقة 2</h2>
                        <p>هذا نص تجريبي للبطاقة الثانية</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-2">بطاقة 3</h2>
                        <p>هذا نص تجريبي للبطاقة الثالثة</p>
                    </div>
                </div>

                <div class="mt-8">
                    <button class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        زر تجريبي
                    </button>
                </div>

                <div class="mt-8 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    ✅ إذا رأيت هذه الرسالة بشكل جيد، فإن CSS يعمل بنجاح!
                </div>
            </div>
        </div>
    </div>
</body>
</html>
