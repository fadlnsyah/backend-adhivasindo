<?php

namespace Tests\Feature\Content;

use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdateContentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_owner_can_update_content(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $content = Content::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old Title',
            'content' => 'Old content body.',
            'image' => null,
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/contents/{$content->id}", [
                'title' => 'Updated Title',
                'content' => 'Updated content body.',
                'image' => 'https://example.com/updated.jpg',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Content updated successfully')
            ->assertJsonPath('data.id', $content->id)
            ->assertJsonPath('data.title', 'Updated Title')
            ->assertJsonPath('data.content', 'Updated content body.')
            ->assertJsonPath('data.image', 'https://example.com/updated.jpg')
            ->assertJsonPath('data.author.id', $user->id);

        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
            'user_id' => $user->id,
            'title' => 'Updated Title',
            'content' => 'Updated content body.',
            'image' => 'https://example.com/updated.jpg',
        ]);
    }

    public function test_update_content_requires_valid_payload(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $content = Content::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/contents/{$content->id}", [
                'title' => '',
                'content' => '',
                'image' => str_repeat('a', 256),
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation Error')
            ->assertJsonValidationErrors(['title', 'content', 'image']);
    }

    public function test_guest_cannot_update_content(): void
    {
        $content = Content::factory()->create();

        $response = $this->putJson("/api/contents/{$content->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated content body.',
        ]);

        $response->assertUnauthorized();
    }

    public function test_non_owner_cannot_update_content(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = auth('api')->login($otherUser);
        $content = Content::factory()->create([
            'user_id' => $owner->id,
            'title' => 'Original Title',
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/contents/{$content->id}", [
                'title' => 'Forbidden Update',
                'content' => 'This should not be saved.',
            ]);

        $response
            ->assertForbidden()
            ->assertExactJson([
                'success' => false,
                'message' => 'Forbidden',
            ]);

        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
            'title' => 'Original Title',
        ]);
    }

    public function test_update_content_returns_not_found(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/contents/999999', [
                'title' => 'Missing Content',
                'content' => 'Missing content body.',
            ]);

        $response
            ->assertNotFound()
            ->assertExactJson([
                'success' => false,
                'message' => 'Content not found',
            ]);
    }
}
