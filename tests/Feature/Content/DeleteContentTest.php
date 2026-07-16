<?php

namespace Tests\Feature\Content;

use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeleteContentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_owner_can_delete_content(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $content = Content::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/contents/{$content->id}");

        $response
            ->assertOk()
            ->assertExactJson([
                'success' => true,
                'message' => 'Content deleted successfully',
                'data' => null,
            ]);

        $this->assertDatabaseMissing('contents', [
            'id' => $content->id,
        ]);
    }

    public function test_guest_cannot_delete_content(): void
    {
        $content = Content::factory()->create();

        $response = $this->deleteJson("/api/contents/{$content->id}");

        $response->assertUnauthorized();

        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
        ]);
    }

    public function test_non_owner_cannot_delete_content(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = auth('api')->login($otherUser);
        $content = Content::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/contents/{$content->id}");

        $response
            ->assertForbidden()
            ->assertExactJson([
                'success' => false,
                'message' => 'Forbidden',
            ]);

        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
        ]);
    }

    public function test_delete_content_returns_not_found(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson('/api/contents/999999');

        $response
            ->assertNotFound()
            ->assertExactJson([
                'success' => false,
                'message' => 'Content not found',
            ]);
    }
}
