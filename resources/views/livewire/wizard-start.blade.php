<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" dir="rtl">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">ابدأ خطة عملك الآن</h1>
            <p class="text-xl text-gray-600">اختر قالباً جاهزاً أو ابدأ من الصفر</p>
        </div>

        <!-- Templates Grid -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">القوالب الجاهزة</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($templates as $template)
                <div wire:click="selectTemplate({{ $template->id }})"
                     class="bg-white rounded-lg shadow-md p-6 cursor-pointer transition-all hover:shadow-xl {{ $selectedTemplate == $template->id ? 'ring-2 ring-blue-500 border-blue-500' : 'border border-gray-200' }}">
                    @if($template->is_featured)
                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full mb-3">مميز</span>
                    @endif
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $template->name }}</h3>
                    <p class="text-gray-600 text-sm mb-3">{{ $template->description }}</p>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        {{ $template->industry_type }}
                    </div>
                </div>
                @endforeach

                <!-- Custom Template Option -->
                <div wire:click="selectCustom"
                     class="bg-white rounded-lg shadow-md p-6 cursor-pointer transition-all hover:shadow-xl border-2 border-dashed {{ $showCustomForm ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                    <div class="flex flex-col items-center justify-center h-full text-center">
                        <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">ابدأ من الصفر</h3>
                        <p class="text-gray-600 text-sm">أنشئ خطة مخصصة بالكامل</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        @if($selectedTemplate || $showCustomForm)
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-2xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">معلومات أساسية</h2>

            <form wire:submit.prevent="startWizard">
                <!-- Company Name -->
                <div class="mb-6">
                    <label for="companyName" class="block text-sm font-medium text-gray-700 mb-2">
                        اسم الشركة أو المشروع <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="companyName"
                           id="companyName"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="مثال: شركة التقنية المتقدمة">
                    @error('companyName')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Project Type -->
                <div class="mb-6">
                    <label for="projectType" class="block text-sm font-medium text-gray-700 mb-2">
                        نوع المشروع <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="projectType"
                            id="projectType"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="new_business">مشروع جديد</option>
                        <option value="existing_expansion">توسع مشروع قائم</option>
                        <option value="franchise">امتياز تجاري</option>
                        <option value="startup">شركة ناشئة</option>
                    </select>
                    @error('projectType')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Industry Type -->
                <div class="mb-6">
                    <label for="industryType" class="block text-sm font-medium text-gray-700 mb-2">
                        نوع الصناعة <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="industryType"
                           id="industryType"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="مثال: تقنية المعلومات، مطاعم، تعليم">
                    @error('industryType')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-between items-center pt-6 border-t">
                    <button type="button"
                            wire:click="goBack"
                            class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        العودة
                    </button>
                    <button type="submit"
                            class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                        البدء في إنشاء الخطة
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>
