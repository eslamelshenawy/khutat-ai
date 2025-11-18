<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8" dir="rtl">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">إنشاء خطة عمل جديدة</h1>
            <p class="text-xl text-gray-600">ابدأ رحلتك نحو إنشاء خطة عمل احترافية بمساعدة الذكاء الاصطناعي</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Step 1: Select Template -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">1. اختر قالب مناسب لمشروعك</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($templates as $template)
                        <div
                            wire:click="selectTemplate({{ $template->id }})"
                            class="cursor-pointer p-6 border-2 rounded-lg transition-all duration-200 hover:shadow-lg
                                {{ $selectedTemplateId == $template->id ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300' }}">
                            @if($template->thumbnail)
                                <img src="{{ $template->thumbnail }}" alt="{{ $template->name }}" class="w-full h-32 object-cover rounded-md mb-3">
                            @endif
                            <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $template->name }}</h3>
                            <p class="text-sm text-gray-600 mb-2">{{ $template->description }}</p>
                            <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">{{ $template->industry_type }}</span>
                        </div>
                    @endforeach
                </div>
                @error('selectedTemplateId') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Step 2: Basic Information -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">2. معلومات أساسية عن مشروعك</h2>

                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الخطة *</label>
                        <input
                            wire:model="title"
                            type="text"
                            placeholder="مثال: خطة عمل مطعم سريع"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم الشركة *</label>
                        <input
                            wire:model="companyName"
                            type="text"
                            placeholder="مثال: شركة النجاح للمطاعم"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('companyName') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Project Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع المشروع *</label>
                        <select
                            wire:model="projectType"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="new_business">مشروع جديد</option>
                            <option value="existing_expansion">توسع لمشروع قائم</option>
                            <option value="franchise">امتياز تجاري</option>
                            <option value="startup">شركة ناشئة</option>
                        </select>
                        @error('projectType') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Industry Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع الصناعة *</label>
                        <input
                            wire:model="industryType"
                            type="text"
                            placeholder="مثال: مطاعم، تقنية، تجزئة"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('industryType') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">وصف مختصر (اختياري)</label>
                        <textarea
                            wire:model="description"
                            rows="4"
                            placeholder="اكتب وصفاً مختصراً عن فكرة مشروعك..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                        @error('description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button
                    wire:click="startWizard"
                    class="px-8 py-4 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                    ابدأ إنشاء الخطة
                </button>
            </div>
        </div>
    </div>
</div>
