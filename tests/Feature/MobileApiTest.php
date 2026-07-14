<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Website::truncate();
    }

    public function test_returns_published_website_with_theme_and_sections()
    {
        Website::factory()->create([
            'name' => 'My Restaurant',
            'business_type' => 'Restaurant',
            'template' => 'classic',
            'status' => 'published',
            'theme' => [
                'primary' => '#1a1a2e',
                'secondary' => '#e94560',
            ],
            'pages' => [
                ['title' => 'Home', 'type' => 'hero'],
                ['title' => 'Menu', 'type' => 'menu-list'],
                ['title' => 'Contact', 'type' => 'contact-form'],
            ],
            'slug' => 'my-restaurant',
        ]);

        $response = $this->getJson('/api/mobile/website');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'website' => [
                    'name',
                    'business_type',
                    'template',
                    'slug',
                    'status',
                ],
                'theme',
                'sections',
            ])
            ->assertJson([
                'website' => [
                    'name' => 'My Restaurant',
                    'business_type' => 'Restaurant',
                    'status' => 'published',
                ],
                'theme' => [
                    'primary' => '#1a1a2e',
                    'secondary' => '#e94560',
                ],
                'sections' => [
                    ['title' => 'Home', 'type' => 'hero'],
                    ['title' => 'Menu', 'type' => 'menu-list'],
                    ['title' => 'Contact', 'type' => 'contact-form'],
                ],
            ]);
    }

    public function test_returns_404_when_no_published_website_exists()
    {
        Website::factory()->create([
            'status' => 'draft',
        ]);

        $response = $this->getJson('/api/mobile/website');

        $response->assertStatus(404);
    }

    public function test_ignores_draft_websites()
    {
        Website::factory()->create([
            'name' => 'Draft Site',
            'status' => 'draft',
        ]);

        Website::factory()->create([
            'name' => 'Published Site',
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/mobile/website');

        $response->assertStatus(200);
        $response->assertJsonPath('website.name', 'Published Site');
    }

    public function test_returns_404_when_no_websites_exist_at_all()
    {
        $response = $this->getJson('/api/mobile/website');

        $response->assertStatus(404);
    }
}
