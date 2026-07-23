<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_website()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/websites', [
            'name' => 'My Business Site',
            'business_type' => 'Restaurant',
            'template' => 'modern',
            'theme' => ['primary' => '#ff0000', 'secondary' => '#00ff00'],
            'pages' => [['title' => 'Home'], ['title' => 'Menu']],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Website created successfully.',
                'data' => [
                    'name' => 'My Business Site',
                    'business_type' => 'Restaurant',
                    'status' => 'draft',
                ],
            ])
            ->assertJsonStructure(['data' => ['id', 'slug', 'theme', 'pages']]);

        $this->assertTrue(
            Website::where('name', 'My Business Site')->where('user_id', $user->id)->exists()
        );
    }

    public function test_user_can_list_their_websites()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Website::factory()->count(3)->create(['user_id' => $user->id]);
        Website::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->getJson('/api/websites');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_see_single_website()
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/websites/{$website->id}");

        $response->assertStatus(200)
            ->assertJson(['status' => 'success', 'data' => ['name' => $website->name]]);
    }

    public function test_user_cannot_see_another_users_website()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherWebsite = Website::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->getJson("/api/websites/{$otherWebsite->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_update_own_website()
    {
        $user = User::factory()->create();
        $website = Website::factory()->create([
            'user_id' => $user->id,
            'name' => 'Original Name',
        ]);

        $response = $this->actingAs($user)->putJson("/api/websites/{$website->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success', 'message' => 'Website updated successfully.', 'data' => ['name' => 'Updated Name']]);

        $website->refresh();
        $this->assertEquals('Updated Name', $website->name);
    }

    public function test_user_cannot_update_another_users_website()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherWebsite = Website::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Other Site',
        ]);

        $response = $this->actingAs($user)->putJson("/api/websites/{$otherWebsite->id}", [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_website()
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/websites/{$website->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Website deleted successfully.']);

        $this->assertNull(Website::find($website->id));
    }

    public function test_user_cannot_delete_another_users_website()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherWebsite = Website::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->deleteJson("/api/websites/{$otherWebsite->id}");

        $response->assertStatus(403);

        $this->assertNotNull(Website::find($otherWebsite->id));
    }

    public function test_user_can_publish_own_website()
    {
        $user = User::factory()->create();
        $website = Website::factory()->create([
            'user_id' => $user->id,
            'status' => 'draft',
            'is_published' => false,
        ]);

        $response = $this->actingAs($user)->putJson("/api/websites/{$website->id}/publish", [
            'theme' => [
                'primary_color' => '#6366f1',
                'secondary_color' => '#ec4899',
                'font_family' => 'Inter, sans-serif',
            ],
            'sections' => [
                [
                    'type' => 'hero',
                    'sort_order' => 0,
                    'data' => ['headline' => 'Welcome'],
                    'style' => ['background_color' => '#0f172a'],
                ],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Website published successfully.',
            ]);

        $website->refresh();
        $this->assertEquals('published', $website->status);
        $this->assertTrue($website->is_published);
    }

    public function test_user_cannot_publish_another_users_website()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherWebsite = Website::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->putJson("/api/websites/{$otherWebsite->id}/publish");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_websites()
    {
        $response = $this->getJson('/api/websites');

        $response->assertStatus(401);
    }
}
