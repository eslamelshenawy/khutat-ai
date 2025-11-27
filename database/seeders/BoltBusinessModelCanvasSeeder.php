<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use LaraZeus\Bolt\Models\Category;
use LaraZeus\Bolt\Models\Form;
use LaraZeus\Bolt\Models\Section;
use LaraZeus\Bolt\Models\Field;

class BoltBusinessModelCanvasSeeder extends Seeder
{
    public function run(): void
    {
        // Create Category (simpler approach - only required fields)
        $category = Category::firstOrCreate(
            ['name' => 'Business Planning'],
            [
                'slug' => 'business-planning',
                'description' => 'Forms related to business planning and strategy',
                'ordering' => 1,
            ]
        );

        // Create Form with category_id and dates
        $form = Form::firstOrCreate(
            ['slug' => 'business-model-canvas'],
            [
                'name' => 'Business Model Canvas',
                'description' => 'نموذج رسم الأعمال التجارية - Business Model Canvas',
                'is_active' => true,
                'ordering' => 1,
                'category_id' => $category->id,
                'start_date' => now(),
                'end_date' => now()->addYears(10),
            ]
        );

        // Define the 9 Business Model Canvas steps with Arabic names
        $steps = [
            [
                'name' => 'شرائح العملاء (Customer Segments)',
                'description' => 'من هم عملاؤك المستهدفون؟',
                'fields' => [
                    ['label' => 'من هم العملاء المستهدفون؟', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'ما هي احتياجاتهم الأساسية؟', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'حجم السوق المستهدف', 'type' => 'LaraZeus\\Bolt\\Fields\\Classes\\TextInput', 'required' => false],
                ]
            ],
            [
                'name' => 'عرض القيمة (Value Propositions)',
                'description' => 'ما القيمة التي تقدمها لعملائك؟',
                'fields' => [
                    ['label' => 'ما المشكلة التي تحلها؟', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'ما الفوائد التي تقدمها؟', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'ما يميزك عن المنافسين؟', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                ]
            ],
            [
                'name' => 'القنوات (Channels)',
                'description' => 'كيف ستصل إلى عملائك؟',
                'fields' => [
                    ['label' => 'قنوات التسويق والمبيعات', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'كيف سيعرف العملاء عن منتجك؟', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'طرق التوزيع', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => false],
                ]
            ],
            [
                'name' => 'علاقات العملاء (Customer Relationships)',
                'description' => 'كيف ستبني علاقات مع عملائك؟',
                'fields' => [
                    ['label' => 'نوع العلاقة المطلوبة مع العملاء', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'استراتيجية الاحتفاظ بالعملاء', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'خدمة ما بعد البيع', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => false],
                ]
            ],
            [
                'name' => 'مصادر الإيرادات (Revenue Streams)',
                'description' => 'كيف ستحقق الدخل؟',
                'fields' => [
                    ['label' => 'نموذج التسعير', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'مصادر الدخل المتوقعة', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'الإيرادات المتوقعة سنوياً', 'type' => 'LaraZeus\\Bolt\\Fields\\Classes\\TextInput', 'required' => false],
                ]
            ],
            [
                'name' => 'الموارد الأساسية (Key Resources)',
                'description' => 'ما الموارد التي تحتاجها؟',
                'fields' => [
                    ['label' => 'الموارد المادية المطلوبة', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'الموارد البشرية المطلوبة', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'الموارد التقنية والفكرية', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => false],
                ]
            ],
            [
                'name' => 'الأنشطة الأساسية (Key Activities)',
                'description' => 'ما الأنشطة الضرورية لنجاح عملك؟',
                'fields' => [
                    ['label' => 'الأنشطة الإنتاجية الأساسية', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'الأنشطة التسويقية والبيعية', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'أنشطة إدارية أخرى', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => false],
                ]
            ],
            [
                'name' => 'الشراكات الأساسية (Key Partnerships)',
                'description' => 'من هم شركاؤك؟',
                'fields' => [
                    ['label' => 'الشركاء والموردون الأساسيون', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'التحالفات الاستراتيجية', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => false],
                    ['label' => 'الموارد التي يمكن الحصول عليها من الشركاء', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => false],
                ]
            ],
            [
                'name' => 'هيكل التكاليف (Cost Structure)',
                'description' => 'ما هي التكاليف الأساسية؟',
                'fields' => [
                    ['label' => 'التكاليف الثابتة', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'التكاليف المتغيرة', 'type' => 'LaraZeus\Bolt\Fields\Classes\Textarea', 'required' => true],
                    ['label' => 'إجمالي التكاليف المتوقعة', 'type' => 'LaraZeus\\Bolt\\Fields\\Classes\\TextInput', 'required' => false],
                ]
            ],
        ];

        // Create sections and fields
        $sectionOrder = 1;
        foreach ($steps as $step) {
            $section = Section::firstOrCreate(
                [
                    'form_id' => $form->id,
                    'name' => $step['name'],
                ],
                [
                    'description' => $step['description'],
                    'ordering' => $sectionOrder++,
                ]
            );

            $fieldOrder = 1;
            foreach ($step['fields'] as $fieldData) {
                Field::firstOrCreate(
                    [
                        'section_id' => $section->id,
                        'name' => $fieldData['label'],
                    ],
                    [
                        'type' => $fieldData['type'],
                        'options' => [
                            'label' => $fieldData['label'],
                            'required' => $fieldData['required'],
                            'placeholder' => 'أدخل ' . $fieldData['label'] . '...',
                            'maxLength' => 1000,
                            'rows' => 3,
                            'cols' => null,
                        ],
                        'ordering' => $fieldOrder++,
                    ]
                );
            }
        }

        $this->command->info('✅ Business Model Canvas form created successfully!');
        $this->command->info("📝 Form ID: {$form->id}");
        $this->command->info("🔗 You can access it at: /admin/bolt/forms/{$form->id}/edit");
    }
}
