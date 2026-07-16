<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_login_and_receive_jwt_token(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login successful')
            ->assertJsonPath('data.token_type', 'bearer')
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonPath('data.user.email', 'login@example.com')
            ->assertJsonMissingPath('data.user.password');

        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_login_requires_valid_payload(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation Error')
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'wrong-password@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wrong-password@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertUnauthorized()
            ->assertExactJson([
                'success' => false,
                'message' => 'Invalid credentials',
            ]);
    }
}
