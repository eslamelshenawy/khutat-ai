<?php

namespace Database\Seeders;

use App\Models\WizardStep;
use App\Models\WizardQuestion;
use Illuminate\Database\Seeder;

class WizardStepsSeeder extends Seeder
{
    public function run(): void
    {
        // الخطوة 1: الشركاء الرئيسيين
        $step1 = WizardStep::create([
            'title' => 'الشركاء الرئيسيين',
            'description' => 'من هم الشركاء المفتاحيون لإنجاز منتجك؟',
            'icon' => '🤝',
            'order' => 1,
            'is_active' => true,
        ]);

        WizardQuestion::create([
            'wizard_step_id' => $step1->id,
            'label' => 'من هم الشركاء المفتاحيون الذين يحتاجهم مشروعك لإنجاز منتجك؟',
            'help_text' => 'الموردون، المستثمرون، الشركات، الجهات الحكومية... إلخ',
            'type' => 'textarea',
            'field_name' => 'key_partners',
            'is_required' => true,
            'order' => 1,
            'is_active' => true,
        ]);

        // الخطوة 2: الأنشطة الرئيسية
        $step2 = WizardStep::create([
            'title' => 'الأنشطة الرئيسية',
            'description' => 'ما الأنشطة التي ستركز عليها لتقوم بـ "براعة" و "كفاءة"؟',
            'icon' => '⚙️',
            'order' => 2,
            'is_active' => true,
        ]);

        WizardQuestion::create([
            'wizard_step_id' => $step2->id,
            'label' => 'ما الأنشطة التي ستركز عليها لتقوم بـ "براعة" وكفاءة بإنتاج منتجك؟',
            'help_text' => 'مثال: الإنتاج، التسويق، البيع، خدمة العملاء...',
            'type' => 'textarea',
            'field_name' => 'key_activities',
            'is_required' => true,
            'order' => 1,
            'is_active' => true,
        ]);

        // الخطوة 3: القيمة المضافة
        $step3 = WizardStep::create([
            'title' => 'القيمة المضافة',
            'description' => 'ماذا تقدم من "الفريد" للعملاء؟',
            'icon' => '🎁',
            'order' => 3,
            'is_active' => true,
        ]);

        WizardQuestion::create([
            'wizard_step_id' => $step3->id,
            'label' => 'ماذا تقدم من "الفريد" الذي يستهدفه العملاء؟',
            'help_text' => 'القيمة التي تميزك عن المنافسين',
            'type' => 'textarea',
            'field_name' => 'value_proposition',
            'is_required' => true,
            'order' => 1,
            'is_active' => true,
        ]);

        // الخطوة 4: علاقات العملاء
        $step4 = WizardStep::create([
            'title' => 'علاقات العملاء',
            'description' => 'كيف تبني وتحافظ على علاقات طويلة الأمد؟',
            'icon' => '🤗',
            'order' => 4,
            'is_active' => true,
        ]);

        WizardQuestion::create([
            'wizard_step_id' => $step4->id,
            'label' => 'كيف ستبني وتحافظ على علاقات طويلة الأمد مع العملاء؟',
            'help_text' => 'مثال: خدمة عملاء ممتازة، برامج ولاء، متابعة دورية...',
            'type' => 'textarea',
            'field_name' => 'customer_relationships',
            'is_required' => true,
            'order' => 1,
            'is_active' => true,
        ]);

        // الخطوة 5: شرائح المستخدمين
        $step5 = WizardStep::create([
            'title' => 'شرائح المستخدمين',
            'description' => 'لمن تقدم العملاء؟ ولماذا؟',
            'icon' => '👥',
            'order' => 5,
            'is_active' => true,
        ]);

        WizardQuestion::create([
            'wizard_step_id' => $step5->id,
            'label' => 'من هم فئة من المستخدمين الذين تستهدفهم؟',
            'help_text' => 'حدد الشرائح المستهدفة بدقة (العمر، الموقع، الاهتمامات...)',
            'type' => 'textarea',
            'field_name' => 'customer_segments',
            'is_required' => true,
            'order' => 1,
            'is_active' => true,
        ]);

        // الخطوة 6: الموارد الرئيسية
        $step6 = WizardStep::create([
            'title' => 'الموارد الرئيسية',
            'description' => 'ما الموارد التي تحتاجها لنجاح مشروعك؟',
            'icon' => '💎',
            'order' => 6,
            'is_active' => true,
        ]);

        WizardQuestion::create([
            'wizard_step_id' => $step6->id,
            'label' => 'ما الموارد (الأدوات، العمالة، رأس المال) التي تحتاجها لنجاح مشروعك؟',
            'help_text' => 'موارد مادية، بشرية، فكرية، مالية...',
            'type' => 'textarea',
            'field_name' => 'key_resources',
            'is_required' => true,
            'order' => 1,
            'is_active' => true,
        ]);

        // الخطوة 7: قنوات التواصل
        $step7 = WizardStep::create([
            'title' => 'قنوات التواصل',
            'description' => 'كيف ستصل إلى عملائك؟',
            'icon' => '📢',
            'order' => 7,
            'is_active' => true,
        ]);

        WizardQuestion::create([
            'wizard_step_id' => $step7->id,
            'label' => 'من خلال أي قنوات ستتواصل مع العملاء وتبيع منتجك؟',
            'help_text' => 'مثال: متجر إلكتروني، وسائل التواصل الاجتماعي، نقاط البيع...',
            'type' => 'textarea',
            'field_name' => 'channels',
            'is_required' => true,
            'order' => 1,
            'is_active' => true,
        ]);

        // الخطوة 8: هيكل التكاليف
        $step8 = WizardStep::create([
            'title' => 'هيكل التكاليف',
            'description' => 'ما هي التكاليف الأساسية لتشغيل مشروعك؟',
            'icon' => '💰',
            'order' => 8,
            'is_active' => true,
        ]);

        WizardQuestion::create([
            'wizard_step_id' => $step8->id,
            'label' => 'ما عناصر التكاليف الأساسية في نموذج عملك؟',
            'help_text' => 'تكاليف ثابتة ومتغيرة: الرواتب، الإيجار، المواد الخام، التسويق...',
            'type' => 'textarea',
            'field_name' => 'cost_structure',
            'is_required' => true,
            'order' => 1,
            'is_active' => true,
        ]);

        // الخطوة 9: مصادر الدخل / الإيرادات
        $step9 = WizardStep::create([
            'title' => 'مصادر الدخل / الإيرادات',
            'description' => 'كيف ستحقق الدخل من عملائك؟',
            'icon' => '💵',
            'order' => 9,
            'is_active' => true,
        ]);

        WizardQuestion::create([
            'wizard_step_id' => $step9->id,
            'label' => 'ما أهم مصادر وطرق ومصادر الإيرادات والمصادر؟',
            'help_text' => 'مثال: بيع المنتج، اشتراكات، إعلانات، عمولات...',
            'type' => 'textarea',
            'field_name' => 'revenue_streams',
            'is_required' => true,
            'order' => 1,
            'is_active' => true,
        ]);

        $this->command->info('✅ تم إنشاء 9 خطوات مع أسئلتها بنجاح!');
    }
}
