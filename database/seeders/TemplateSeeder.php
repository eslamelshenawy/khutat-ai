<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'مطعم أو مقهى',
                'slug' => 'restaurant-cafe',
                'industry_type' => 'مطاعم ومقاهي',
                'description' => 'قالب مخصص للمطاعم والمقاهي مع التركيز على قوائم الطعام، الموقع، والخدمة',
                'structure' => [
                    'executive_summary',
                    'company_description',
                    'menu_services',
                    'market_analysis',
                    'marketing_strategy',
                    'financial_projections',
                    'operations_plan'
                ],
                'ai_prompts' => [
                    'executive_summary' => 'اكتب ملخصاً تنفيذياً احترافياً لمطعم {company_name} الذي يقدم {description}',
                    'market_analysis' => 'قم بتحليل سوق المطاعم في المنطقة المستهدفة مع التركيز على {target_market}',
                ],
                'custom_questions' => [
                    'نوع المطبخ؟',
                    'حجم المطعم (عدد الطاولات)؟',
                    'نموذج الخدمة (تناول في المكان، توصيل، take-away)؟'
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'متجر إلكتروني',
                'slug' => 'ecommerce-store',
                'industry_type' => 'تجارة إلكترونية',
                'description' => 'قالب للمتاجر الإلكترونية والتجارة عبر الإنترنت',
                'structure' => [
                    'executive_summary',
                    'products_catalog',
                    'target_audience',
                    'marketing_digital_strategy',
                    'logistics_shipping',
                    'financial_projections',
                    'technology_stack'
                ],
                'ai_prompts' => [
                    'executive_summary' => 'اكتب ملخصاً تنفيذياً لمتجر إلكتروني {company_name} متخصص في {description}',
                    'marketing_digital_strategy' => 'ضع استراتيجية تسويق رقمي شاملة تشمل SEO، وسائل التواصل، والإعلانات المدفوعة',
                ],
                'custom_questions' => [
                    'نوع المنتجات المباعة؟',
                    'منصة التجارة الإلكترونية المستخدمة؟',
                    'طرق الدفع المتاحة؟'
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'شركة تقنية ناشئة',
                'slug' => 'tech-startup',
                'industry_type' => 'تقنية',
                'description' => 'قالب للشركات الناشئة في مجال التقنية والبرمجيات',
                'structure' => [
                    'executive_summary',
                    'problem_solution',
                    'product_description',
                    'market_opportunity',
                    'business_model',
                    'competitive_analysis',
                    'team_structure',
                    'financial_projections',
                    'funding_requirements'
                ],
                'ai_prompts' => [
                    'problem_solution' => 'وصّف المشكلة التي يحلها منتجك {company_name} والحل المبتكر الذي تقدمه',
                    'market_opportunity' => 'حلل فرصة السوق لمنتج {description} في المنطقة المستهدفة',
                ],
                'custom_questions' => [
                    'نوع المنتج التقني (SaaS, Mobile App, Platform)؟',
                    'مرحلة التطوير الحالية؟',
                    'نموذج تحقيق الإيرادات؟'
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'مركز تدريب',
                'slug' => 'training-center',
                'industry_type' => 'تعليم وتدريب',
                'description' => 'قالب لمراكز التدريب والدورات التعليمية',
                'structure' => [
                    'executive_summary',
                    'courses_programs',
                    'target_students',
                    'instructors_team',
                    'facilities_equipment',
                    'marketing_strategy',
                    'financial_projections'
                ],
                'ai_prompts' => [
                    'courses_programs' => 'صف البرامج التدريبية التي يقدمها {company_name} في مجال {description}',
                    'target_students' => 'حدد الجمهور المستهدف من المتدربين واحتياجاتهم التدريبية',
                ],
                'custom_questions' => [
                    'أنواع الدورات المقدمة؟',
                    'نمط التدريب (حضوري، أونلاين، مختلط)؟',
                    'الشهادات المقدمة؟'
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'صالون تجميل',
                'slug' => 'beauty-salon',
                'industry_type' => 'خدمات تجميل',
                'description' => 'قالب لصالونات التجميل ومراكز العناية',
                'structure' => [
                    'executive_summary',
                    'services_offered',
                    'target_market',
                    'location_setup',
                    'pricing_strategy',
                    'marketing_plan',
                    'financial_projections'
                ],
                'ai_prompts' => [
                    'services_offered' => 'اذكر خدمات التجميل والعناية التي يقدمها {company_name}',
                    'pricing_strategy' => 'ضع استراتيجية تسعير تنافسية لخدمات الصالون',
                ],
                'custom_questions' => [
                    'الخدمات الرئيسية (شعر، أظافر، مكياج، إلخ)؟',
                    'الجنس المستهدف (نساء، رجال، للجنسين)؟',
                    'عدد الموظفين المخطط؟'
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'خدمات استشارية',
                'slug' => 'consulting-services',
                'industry_type' => 'استشارات',
                'description' => 'قالب للشركات الاستشارية في مختلف المجالات',
                'structure' => [
                    'executive_summary',
                    'expertise_services',
                    'target_clients',
                    'service_delivery',
                    'team_qualifications',
                    'pricing_model',
                    'marketing_strategy',
                    'financial_projections'
                ],
                'ai_prompts' => [
                    'expertise_services' => 'وصف الخدمات الاستشارية المتخصصة التي تقدمها {company_name}',
                    'target_clients' => 'حدد العملاء المستهدفين وأنواع الشركات التي تخدمها',
                ],
                'custom_questions' => [
                    'مجال التخصص الاستشاري؟',
                    'نموذج الأتعاب (ساعي، مشروع، عقد سنوي)؟',
                    'خبرة الفريق الاستشاري؟'
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 6,
            ],
        ];

        foreach ($templates as $template) {
            Template::create($template);
        }
    }
}
