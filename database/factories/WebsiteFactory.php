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
            'business_type' => fake()->randomElement(['Restaurant', 'Salon', 'Portfolio', 'Store']),
            'template' => fake()->randomElement(['modern', 'classic', 'minimal']),
            'theme' => [
                'primary' => fake()->hexColor(),
                'secondary' => fake()->hexColor(),
            ],
            'status' => 'draft',
            'slug' => Str::slug(fake()->company()) . '-' . Str::random(6),
            'pages' => [
                ['title' => 'Home', 'type' => 'hero'],
                ['title' => 'About', 'type' => 'text'],
            ],
        ];
    }
}
