<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_image_to_own_website()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->image('logo.jpg');

        $response = $this->actingAs($user)->postJson("/api/websites/{$website->id}/media", [
            'file' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'filename', 'path', 'size', 'url']);

        Storage::disk('public')->assertExists($response->json('path'));

        $this->assertTrue(
            Media::where('website_id', $website->id)
                ->where('filename', $response->json('filename'))
                ->exists()
        );
    }

    public function test_upload_fails_with_non_image_file()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($user)->postJson("/api/websites/{$website->id}/media", [
            'file' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    public function test_upload_fails_with_oversized_file()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->image('large.jpg')->size(3072);

        $response = $this->actingAs($user)->postJson("/api/websites/{$website->id}/media", [
            'file' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    public function test_user_cannot_upload_to_another_users_website()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherWebsite = Website::factory()->create(['user_id' => $otherUser->id]);

        $file = UploadedFile::fake()->image('logo.jpg');

        $response = $this->actingAs($user)->postJson("/api/websites/{$otherWebsite->id}/media", [
            'file' => $file,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_list_media_for_own_website()
    {
        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        Media::factory()->count(3)->create(['website_id' => $website->id]);

        $response = $this->actingAs($user)->getJson("/api/websites/{$website->id}/media");

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_user_cannot_list_media_for_another_users_website()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherWebsite = Website::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->getJson("/api/websites/{$otherWebsite->id}/media");

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_media()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $website = Website::factory()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->image('logo.jpg');
        $path = $file->store('uploads', 'public');

        $media = Media::factory()->create([
            'website_id' => $website->id,
            'path' => $path,
            'filename' => $file->hashName(),
            'size' => $file->getSize(),
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/websites/{$website->id}/media/{$media->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Media deleted successfully.']);

        Storage::disk('public')->assertMissing($path);

        $this->assertNull(Media::find($media->id));
    }

    public function test_user_cannot_delete_another_users_media()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherWebsite = Website::factory()->create(['user_id' => $otherUser->id]);

        $media = Media::factory()->create(['website_id' => $otherWebsite->id]);

        $response = $this->actingAs($user)->deleteJson("/api/websites/{$otherWebsite->id}/media/{$media->id}");

        $response->assertStatus(403);
    }
}
