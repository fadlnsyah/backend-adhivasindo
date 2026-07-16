<?php

namespace Tests\Feature\Content;

use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GetContentDetailTest extends TestCase
{
    use DatabaseTransactions;

    public function test_authenticated_user_can_get_content_detail(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $content = Content::factory()->create([
            'user_id' => $user->id,
            'title' => 'Detail Content',
            'content' => 'This is the detail content body.',
            'image' => null,
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/contents/{$content->id}");

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Content retrieved successfully')
            ->assertJsonPath('data.id', $content->id)
            ->assertJsonPath('data.title', 'Detail Content')
            ->assertJsonPath('data.content', 'This is the detail content body.')
            ->assertJsonPath('data.image', null)
            ->assertJsonPath('data.author.id', $user->id)
            ->assertJsonPath('data.author.name', $user->name);
    }

    public function test_get_content_detail_returns_not_found(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/contents/999999');

        $response
            ->assertNotFound()
            ->assertExactJson([
                'success' => false,
                'message' => 'Content not found',
            ]);
    }

    public function test_guest_cannot_get_content_detail(): void
    {
        $content = Content::factory()->create();

        $response = $this->getJson("/api/contents/{$content->id}");

        $response->assertUnauthorized();
    }
}
