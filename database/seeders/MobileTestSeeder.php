<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MobileTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'demo@launchcraft.app',
            'password' => Hash::make('password'),
        ]);

        $token = $user->createToken('mobile-test-token')->plainTextToken;

        $this->command->info('User created: demo@launchcraft.app / password');
        $this->command->info('Sanctum Token: ' . $token);

        Website::create([
            'user_id' => $user->id,
            'name' => 'LaunchCraft Demo',
            'domain' => 'demo.launchcraft.app',
            'business_type' => 'SaaS',
            'template' => 'modern',
            'is_published' => true,
            'status' => 'published',
            'slug' => 'launchcraft-demo',
            'theme' => [
                'primary_color' => '#6366f1',
                'secondary_color' => '#ec4899',
                'font_family' => 'Inter, sans-serif',
                'border_radius' => '12px',
                'spacing' => '24px',
            ],
            'sections' => [
                [
                    'type' => 'hero',
                    'sort_order' => 0,
                    'data' => [
                        'headline' => 'Build Your Dream Website',
                        'subheadline' => 'Launch, manage, and grow your online presence in minutes.',
                        'cta_text' => 'Get Started Free',
                        'cta_link' => '/signup',
                        'background_image' => 'https://picsum.photos/seed/hero/1200/600',
                    ],
                    'style' => [
                        'background_color' => '#0f172a',
                        'text_color' => '#ffffff',
                        'padding_vertical' => '80px',
                        'padding_horizontal' => '24px',
                        'alignment' => 'center',
                    ],
                ],
                [
                    'type' => 'features',
                    'sort_order' => 1,
                    'data' => [
                        'title' => 'Everything You Need',
                        'items' => [
                            [
                                'icon' => 'layout-dashboard',
                                'heading' => 'Drag & Drop Builder',
                                'description' => 'Build stunning pages without writing a single line of code.',
                            ],
                            [
                                'icon' => 'smartphone',
                                'heading' => 'Mobile Optimized',
                                'description' => 'Your website looks perfect on every device, automatically.',
                            ],
                            [
                                'icon' => 'zap',
                                'heading' => 'Lightning Fast',
                                'description' => 'Blazing performance with our optimized infrastructure.',
                            ],
                        ],
                    ],
                    'style' => [
                        'background_color' => '#ffffff',
                        'text_color' => '#1e293b',
                        'accent_color' => '#6366f1',
                        'padding_vertical' => '60px',
                        'padding_horizontal' => '24px',
                        'grid_columns' => 3,
                        'gap' => '32px',
                    ],
                ],
                [
                    'type' => 'footer',
                    'sort_order' => 2,
                    'data' => [
                        'copyright' => '© 2026 LaunchCraft. All rights reserved.',
                        'links' => [
                            ['label' => 'Privacy Policy', 'url' => '/privacy'],
                            ['label' => 'Terms of Service', 'url' => '/terms'],
                            ['label' => 'Contact Us', 'url' => '/contact'],
                        ],
                        'social' => [
                            ['platform' => 'twitter', 'url' => 'https://twitter.com/launchcraft'],
                            ['platform' => 'github', 'url' => 'https://github.com/launchcraft'],
                        ],
                    ],
                    'style' => [
                        'background_color' => '#0f172a',
                        'text_color' => '#94a3b8',
                        'link_color' => '#6366f1',
                        'padding_vertical' => '40px',
                        'padding_horizontal' => '24px',
                        'alignment' => 'center',
                    ],
                ],
            ],
        ]);

        $this->command->info('Mock website "LaunchCraft Demo" created with 3 sections.');
    }
}
