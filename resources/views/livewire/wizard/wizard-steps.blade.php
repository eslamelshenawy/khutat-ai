<div class="min-h-screen bg-gray-50 py-8" dir="rtl">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">{{ $businessPlan->title }}</h2>
                <span class="text-sm text-gray-600">الخطوة {{ $currentStep }} من {{ $totalSteps }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-indigo-600 h-3 rounded-full transition-all duration-300"
                     style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
            </div>
        </div>

        <!-- Step Content -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            @if($currentStep == 1)
                <h3 class="text-2xl font-bold mb-6">الرؤية والرسالة</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium mb-2">الرؤية *</label>
                        <textarea wire:model="vision" rows="4"
                                  class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                                  placeholder="ما هي رؤيتك طويلة المدى للشركة؟"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">الرسالة *</label>
                        <textarea wire:model="mission" rows="4"
                                  class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                                  placeholder="ما هي رسالة شركتك؟"></textarea>
                    </div>
                </div>
            @elseif($currentStep == 2)
                <h3 class="text-2xl font-bold mb-6">السوق المستهدف</h3>
                <div>
                    <label class="block text-sm font-medium mb-2">وصف السوق المستهدف *</label>
                    <textarea wire:model="targetMarket" rows="6"
                              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="من هم عملاؤك المستهدفون؟ ما هي خصائصهم؟"></textarea>
                </div>
            @elseif($currentStep == 3)
                <h3 class="text-2xl font-bold mb-6">الميزة التنافسية</h3>
                <div>
                    <label class="block text-sm font-medium mb-2">ما يميزك عن المنافسين *</label>
                    <textarea wire:model="competitiveAdvantage" rows="6"
                              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="ما الذي يجعل منتجك أو خدمتك مميزة؟"></textarea>
                </div>
            @elseif($currentStep == 4)
                <h3 class="text-2xl font-bold mb-6">المنتجات والخدمات</h3>
                <div>
                    <label class="block text-sm font-medium mb-2">قائمة المنتجات/الخدمات *</label>
                    <textarea wire:model="products" rows="6"
                              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="اذكر المنتجات أو الخدمات التي ستقدمها"></textarea>
                </div>
            @elseif($currentStep == 5)
                <h3 class="text-2xl font-bold mb-6">استراتيجية التسويق</h3>
                <div>
                    <label class="block text-sm font-medium mb-2">خطة التسويق *</label>
                    <textarea wire:model="marketingStrategy" rows="6"
                              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="كيف ستسوق لمنتجاتك؟ ما هي القنوات التي ستستخدمها؟"></textarea>
                </div>
            @elseif($currentStep == 6)
                <h3 class="text-2xl font-bold mb-6">التوقعات المالية</h3>
                <div>
                    <label class="block text-sm font-medium mb-2">التوقعات المالية الأولية *</label>
                    <textarea wire:model="financialProjections" rows="6"
                              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="ما هي توقعاتك للإيرادات والتكاليف؟"></textarea>
                </div>
            @elseif($currentStep == 7)
                <h3 class="text-2xl font-bold mb-6">الهيكل التنظيمي</h3>
                <div>
                    <label class="block text-sm font-medium mb-2">الفريق والهيكل التنظيمي *</label>
                    <textarea wire:model="teamStructure" rows="6"
                              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="من هم أعضاء فريقك الرئيسيون؟ ما هو الهيكل التنظيمي؟"></textarea>
                </div>
            @else
                <h3 class="text-2xl font-bold mb-6">مراجعة نهائية</h3>
                <p class="text-gray-600 mb-4">لقد أكملت جميع الخطوات! راجع المعلومات قبل إنشاء الفصول.</p>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-green-800 font-medium">جاهز لإنشاء فصول خطة العمل</p>
                </div>
            @endif
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-8">
            <button wire:click="previousStep"
                    @if($currentStep == 1) disabled @endif
                    class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 disabled:opacity-50 disabled:cursor-not-allowed">
                السابق
            </button>

            @if($currentStep < $totalSteps)
                <button wire:click="nextStep"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    التالي
                </button>
            @else
                <button wire:click="finishWizard"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    إنهاء وإنشاء الفصول
                </button>
            @endif
        </div>
    </div>
</div>
