<?php

namespace Tests\Feature\Content;

use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ListContentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_authenticated_user_can_get_paginated_contents(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        Content::factory()->count(12)->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/contents');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Contents retrieved successfully')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 12)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'image',
                        'author' => ['id', 'name'],
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_guest_cannot_get_contents(): void
    {
        $response = $this->getJson('/api/contents');

        $response->assertUnauthorized();
    }

    public function test_contents_use_default_pagination(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        Content::factory()->count(15)->create();

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/contents');

        $response
            ->assertOk()
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 15)
            ->assertJsonCount(10, 'data');
    }

    public function test_contents_support_custom_per_page(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        Content::factory()->count(7)->create();

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/contents?per_page=5');

        $response
            ->assertOk()
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 7)
            ->assertJsonCount(5, 'data');
    }

    public function test_contents_limit_per_page_to_one_hundred(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        Content::factory()->count(105)->create();

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/contents?per_page=150');

        $response
            ->assertOk()
            ->assertJsonPath('meta.per_page', 100)
            ->assertJsonPath('meta.total', 105)
            ->assertJsonCount(100, 'data');
    }
}
