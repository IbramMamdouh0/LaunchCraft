<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WebsiteFactory extends Factory
{
    protected $model = Website::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->company(),
            'domain' => fake()->domainName(),
            'business_type' => fake()->randomElement(['Restaurant', 'Salon', 'Portfolio', 'Store']),
            'template' => fake()->randomElement(['modern', 'classic', 'minimal']),
            'theme' => [
                'primary_color' => fake()->hexColor(),
                'secondary_color' => fake()->hexColor(),
                'font_family' => 'Inter, sans-serif',
            ],
            'sections' => [
                [
                    'type' => 'hero',
                    'sort_order' => 0,
                    'data' => ['headline' => fake()->sentence(3), 'subheadline' => fake()->sentence()],
                    'style' => ['background_color' => '#0f172a', 'text_color' => '#ffffff'],
                ],
                [
                    'type' => 'features',
                    'sort_order' => 1,
                    'data' => ['title' => 'Our Features'],
                    'style' => ['background_color' => '#ffffff', 'text_color' => '#1e293b'],
                ],
            ],
            'status' => 'draft',
            'is_published' => false,
            'slug' => Str::slug(fake()->company()) . '-' . Str::random(6),
            'pages' => [
                ['title' => 'Home', 'type' => 'hero'],
                ['title' => 'About', 'type' => 'text'],
            ],
        ];
    }
}
