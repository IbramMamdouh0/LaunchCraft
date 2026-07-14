<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\Website;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'website_id' => Website::factory(),
            'filename' => fake()->word() . '.jpg',
            'path' => 'uploads/' . fake()->word() . '.jpg',
            'size' => fake()->numberBetween(1024, 2048000),
        ];
    }
}
