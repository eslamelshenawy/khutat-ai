<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>مشاركة محمية بكلمة مرور</title>

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
    </style>
</head>
<body class="antialiased bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <!-- Lock Icon -->
            <div class="flex justify-center mb-6">
                <div class="bg-blue-100 rounded-full p-4">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 text-center mb-2">مشاركة محمية</h1>
            <p class="text-gray-600 text-center mb-6">هذه المشاركة محمية بكلمة مرور. الرجاء إدخال كلمة المرور للمتابعة.</p>

            <!-- Error Message -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">
                    <div class="flex">
                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $errors->first('password') }}</span>
                    </div>
                </div>
            @endif

            <!-- Password Form -->
            <form action="{{ route('shared-plan.authenticate', $share->token) }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="أدخل كلمة المرور"
                    >
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                    فتح المشاركة
                </button>
            </form>
        </div>

        <!-- Info -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                إذا كنت لا تملك كلمة المرور، الرجاء التواصل مع الشخص الذي شارك هذا الرابط معك.
            </p>
        </div>
    </div>
</body>
</html>
