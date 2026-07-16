<?php

namespace Tests\Feature\Content;

use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateContentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_authenticated_user_can_create_content(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/contents', [
                'title' => 'First Content',
                'content' => 'This is the first content body.',
                'image' => 'https://example.com/image.jpg',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Content created successfully')
            ->assertJsonPath('data.title', 'First Content')
            ->assertJsonPath('data.content', 'This is the first content body.')
            ->assertJsonPath('data.image', 'https://example.com/image.jpg')
            ->assertJsonPath('data.author.id', $user->id)
            ->assertJsonPath('data.author.name', $user->name);

        $this->assertDatabaseHas('contents', [
            'user_id' => $user->id,
            'title' => 'First Content',
            'content' => 'This is the first content body.',
            'image' => 'https://example.com/image.jpg',
        ]);
    }

    public function test_create_content_ignores_user_id_from_request(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/contents', [
                'user_id' => $otherUser->id,
                'title' => 'Owned Content',
                'content' => 'Content must belong to authenticated user.',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.author.id', $user->id);

        $this->assertDatabaseHas('contents', [
            'user_id' => $user->id,
            'title' => 'Owned Content',
        ]);

        $this->assertDatabaseMissing('contents', [
            'user_id' => $otherUser->id,
            'title' => 'Owned Content',
        ]);
    }

    public function test_create_content_requires_valid_payload(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/contents', [
                'title' => '',
                'content' => '',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation Error')
            ->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_guest_cannot_create_content(): void
    {
        $response = $this->postJson('/api/contents', [
            'title' => 'Guest Content',
            'content' => 'Guest users cannot create content.',
        ]);

        $response->assertUnauthorized();

        $this->assertSame(0, Content::count());
    }
}
