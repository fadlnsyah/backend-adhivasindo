<?php

namespace Tests\Feature\Content;

use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SearchContentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_contents_can_be_searched_by_title(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        Content::factory()->create([
            'title' => 'Laravel Authentication Guide',
            'content' => 'JWT content body.',
        ]);
        Content::factory()->create([
            'title' => 'Vue Component Notes',
            'content' => 'Frontend content body.',
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/contents?search=Laravel');

        $response
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Laravel Authentication Guide');
    }

    public function test_contents_can_be_searched_by_content(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        Content::factory()->create([
            'title' => 'Backend Notes',
            'content' => 'This article explains queue workers.',
        ]);
        Content::factory()->create([
            'title' => 'API Notes',
            'content' => 'This article explains pagination.',
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/contents?search=pagination');

        $response
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.content', 'This article explains pagination.');
    }

    public function test_contents_search_returns_empty_result(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        Content::factory()->create([
            'title' => 'Laravel Authentication Guide',
            'content' => 'JWT content body.',
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/contents?search=nonexistent');

        $response
            ->assertOk()
            ->assertJsonPath('meta.total', 0)
            ->assertJsonCount(0, 'data');
    }
}
