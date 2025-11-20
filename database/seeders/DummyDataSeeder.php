<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BusinessPlan;
use App\Models\Template;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 dummy users
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => "User {$i}",
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                ]
            );
            $users[] = $user;
        }

        $this->command->info('Created/found 10 dummy users');

        // Create templates if they don't exist
        $templates = [
            [
                'name' => 'Technology Startup Template',
                'slug' => 'technology-startup-template',
                'description' => 'A comprehensive template for technology startups',
                'industry_type' => 'Technology',
                'structure' => json_encode([
                    'sections' => [
                        'Executive Summary',
                        'Company Description',
                        'Market Analysis',
                        'Product & Technology',
                        'Marketing Strategy',
                        'Financial Projections',
                    ]
                ]),
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Retail Business Template',
                'slug' => 'retail-business-template',
                'description' => 'Perfect for retail and e-commerce businesses',
                'industry_type' => 'Retail',
                'structure' => json_encode([
                    'sections' => [
                        'Executive Summary',
                        'Business Overview',
                        'Market Research',
                        'Products & Services',
                        'Sales Strategy',
                        'Financial Plan',
                    ]
                ]),
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Restaurant Template',
                'slug' => 'restaurant-template',
                'description' => 'Designed for restaurant and food service businesses',
                'industry_type' => 'Food & Beverage',
                'structure' => json_encode([
                    'sections' => [
                        'Executive Summary',
                        'Restaurant Concept',
                        'Market Analysis',
                        'Menu & Services',
                        'Marketing Plan',
                        'Financial Projections',
                    ]
                ]),
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($templates as $templateData) {
            Template::firstOrCreate(
                ['name' => $templateData['name']],
                $templateData
            );
        }

        $this->command->info('Created ' . count($templates) . ' templates');

        // Business plan statuses and types
        $statuses = ['draft', 'in_progress', 'completed', 'archived'];
        $businessTypes = ['Technology', 'Retail', 'Restaurant', 'Consulting', 'Manufacturing', 'Services'];

        // Create 25 dummy business plans
        $businessPlanNames = [
            'Tech Innovation Hub',
            'Green Energy Solutions',
            'Digital Marketing Agency',
            'E-commerce Platform',
            'Mobile App Development',
            'Cloud Services Provider',
            'AI Consulting Firm',
            'Boutique Coffee Shop',
            'Fitness Center',
            'Online Learning Platform',
            'Sustainable Fashion Brand',
            'Food Delivery Service',
            'Real Estate Management',
            'Healthcare Technology',
            'Smart Home Solutions',
            'Digital Payment System',
            'Content Creation Studio',
            'Coworking Space',
            'Pet Care Services',
            'Eco-Friendly Products',
            'Virtual Event Platform',
            'Language Learning App',
            'Meal Kit Delivery',
            'Home Renovation Services',
            'Subscription Box Service',
        ];

        foreach ($businessPlanNames as $index => $planName) {
            $user = $users[array_rand($users)];
            $template = Template::inRandomOrder()->first();

            BusinessPlan::create([
                'user_id' => $user->id,
                'template_id' => $template?->id,
                'business_name' => $planName,
                'business_type' => $businessTypes[array_rand($businessTypes)],
                'status' => $statuses[array_rand($statuses)],
                'target_market' => $this->getRandomTargetMarket(),
                'funding_goal' => rand(50000, 5000000),
                'data' => json_encode([
                    'vision' => $this->getRandomVision(),
                    'mission' => $this->getRandomMission(),
                    'created_at' => now()->subDays(rand(1, 90))->toDateTimeString(),
                ]),
            ]);
        }

        $this->command->info('Created ' . count($businessPlanNames) . ' business plans');
        $this->command->info('Dummy data seeding completed successfully!');
    }

    private function getRandomTargetMarket(): string
    {
        $markets = [
            'Young professionals aged 25-35',
            'Small and medium businesses',
            'Health-conscious consumers',
            'Tech-savvy millennials',
            'Eco-friendly shoppers',
            'Enterprise clients',
            'Local community members',
            'Remote workers',
            'Families with children',
            'Students and educators',
        ];

        return $markets[array_rand($markets)];
    }

    private function getRandomVision(): string
    {
        $visions = [
            'To become the leading provider in our industry',
            'To revolutionize the way people interact with technology',
            'To create sustainable solutions for future generations',
            'To empower businesses through innovative services',
            'To build a community-focused brand',
            'To deliver exceptional value to our customers',
            'To transform the industry through innovation',
            'To be recognized as the most trusted brand',
        ];

        return $visions[array_rand($visions)];
    }

    private function getRandomMission(): string
    {
        $missions = [
            'Providing high-quality products and services that exceed customer expectations',
            'Delivering innovative solutions that solve real-world problems',
            'Building lasting relationships with clients through exceptional service',
            'Creating value for stakeholders while maintaining ethical standards',
            'Fostering innovation and continuous improvement',
            'Empowering our team to deliver excellence',
            'Driving positive change in our community',
            'Offering sustainable and responsible business practices',
        ];

        return $missions[array_rand($missions)];
    }
}
