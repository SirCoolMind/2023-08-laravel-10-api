<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:client', [
            '--password' => true,
            '--name' => 'Laravel Password Grant Client',
            '--provider' => 'users',
        ]);
        $client = \DB::table('oauth_clients')->where('password_client', 1)->first();
        config([
            'auth.passport_password_client_id' => $client->id,
            'auth.passport_password_client_secret' => $client->secret,
        ]);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'Request was successful.')
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user_data',
                    'user_email',
                    'user_name',
                    'token',
                ]
            ]);
    }

    public function test_login_fails_with_invalid_password()
    {
        User::factory()->create([
            'email' => 'testuser2@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'testuser2@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('status', 'Something is wrong.')
            ->assertJsonPath('data', 'Invalid credentials');
    }

    public function test_login_fails_with_nonexistent_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('status', 'Something is wrong.');
    }
}
