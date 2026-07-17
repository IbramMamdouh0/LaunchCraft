<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use Laravel\Sanctum\Sanctum;
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
        $user = User::factory()->create();
        Website::factory()->create([
            'user_id' => $user->id,
            'name' => 'My Restaurant',
            'domain' => 'my-restaurant.example.com',
            'is_published' => true,
            'theme' => [
                'primary_color' => '#1a1a2e',
                'secondary_color' => '#e94560',
                'font_family' => 'Inter, sans-serif',
            ],
            'sections' => [
                ['type' => 'hero', 'sort_order' => 0, 'data' => ['headline' => 'Welcome'], 'style' => []],
                ['type' => 'features', 'sort_order' => 1, 'data' => ['title' => 'Features'], 'style' => []],
                ['type' => 'footer', 'sort_order' => 2, 'data' => ['copyright' => '2026'], 'style' => []],
            ],
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/mobile/website');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'website' => [
                    'id',
                    'name',
                    'domain',
                    'is_published',
                ],
                'theme',
                'sections',
            ])
            ->assertJson([
                'website' => [
                    'name' => 'My Restaurant',
                    'domain' => 'my-restaurant.example.com',
                    'is_published' => true,
                ],
                'theme' => [
                    'primary_color' => '#1a1a2e',
                    'secondary_color' => '#e94560',
                ],
                'sections' => [
                    ['type' => 'hero', 'sort_order' => 0, 'data' => ['headline' => 'Welcome'], 'style' => []],
                    ['type' => 'features', 'sort_order' => 1, 'data' => ['title' => 'Features'], 'style' => []],
                    ['type' => 'footer', 'sort_order' => 2, 'data' => ['copyright' => '2026'], 'style' => []],
                ],
            ]);
    }

    public function test_returns_404_when_no_published_website_exists()
    {
        $user = User::factory()->create();
        Website::factory()->create([
            'user_id' => $user->id,
            'is_published' => false,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/mobile/website');

        $response->assertStatus(404);
    }

    public function test_ignores_draft_websites()
    {
        $user = User::factory()->create();

        Website::factory()->create([
            'user_id' => $user->id,
            'name' => 'Draft Site',
            'is_published' => false,
        ]);

        Website::factory()->create([
            'user_id' => $user->id,
            'name' => 'Published Site',
            'is_published' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/mobile/website');

        $response->assertStatus(200);
        $response->assertJsonPath('website.name', 'Published Site');
    }

    public function test_returns_404_when_no_websites_exist_at_all()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/mobile/website');

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_access_mobile_endpoint()
    {
        $response = $this->getJson('/api/mobile/website');

        $response->assertStatus(401);
    }

    public function test_returns_only_own_published_website()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Website::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Other User Site',
            'is_published' => true,
        ]);

        Website::factory()->create([
            'user_id' => $user->id,
            'name' => 'My Site',
            'is_published' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/mobile/website');

        $response->assertStatus(200);
        $response->assertJsonPath('website.name', 'My Site');
    }
}
