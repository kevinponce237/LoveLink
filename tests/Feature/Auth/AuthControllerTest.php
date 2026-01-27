<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // RefreshDatabase trait handles database setup
    }

    /**
     * Test user can login with valid credentials - Returns 200 with token and user
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'email',
                'name',
            ],
            'token'
        ]);

        $this->assertEquals('test@example.com', $response->json('user.email'));
        $this->assertNotEmpty($response->json('token'));
    }

    /**
     * Test user can register with valid data - Returns 201 with user and token
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'name',
                'email',
            ],
            'token'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe'
        ]);

        $this->assertEquals('john@example.com', $response->json('user.email'));
        $this->assertNotEmpty($response->json('token'));
    }

    /**
     * Test authenticated user can logout - Returns 200 confirmation
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Sesión cerrada exitosamente.'
        ]);

        // Verify tokens were revoked
        $this->assertEquals(0, $user->tokens()->count());
    }

    /**
     * Test authenticated user can get profile - Returns 200 with user data
     */
    public function test_authenticated_user_can_get_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
            ]
        ]);

        $this->assertEquals('john@example.com', $response->json('user.email'));
        $this->assertEquals('John Doe', $response->json('user.name'));
    }

    /**
     * Test login fails with invalid credentials - Returns 422 Unprocessable Entity
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct_password')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Las credenciales proporcionadas son incorrectas.',
            'errors' => [
                'email' => ['Las credenciales no son válidas.']
            ]
        ]);
    }

    /**
     * Test login requires email and password - Returns 422 Validation Errors
     */
    public function test_login_requires_email_and_password(): void
    {
        // Test missing email
        $response = $this->postJson('/api/auth/login', [
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        // Test missing password
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);

        // Test both missing
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test register fails with duplicate email - Returns 422 Email already taken
     */
    public function test_register_fails_with_duplicate_email(): void
    {
        // Create a user first
        User::factory()->create([
            'email' => 'existing@example.com'
        ]);

        // Try to register with same email
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test register requires valid email format - Returns 422 Validation Errors
     */
    public function test_register_requires_valid_email_format(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        // Test with missing email
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        // Test password too short (minimum 8 characters)
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'valid@example.com',
            'password' => 'short'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    /**
     * Test logout requires authentication - Returns 401 Unauthorized
     */
    public function test_logout_requires_authentication(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    /**
     * Test get user requires authentication - Returns 401 Unauthorized
     */
    public function test_get_user_requires_authentication(): void
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }

    /**
     * Test register with name is optional but works when provided
     */
    public function test_register_name_is_optional(): void
    {
        // Test without name
        $response = $this->postJson('/api/auth/register', [
            'email' => 'test1@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'test1@example.com',
            'name' => null
        ]);

        // Test with name
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'test2@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'test2@example.com',
            'name' => 'John Doe'
        ]);
    }

    /**
     * Test login with non-existent user
     */
    public function test_login_with_non_existent_user(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Las credenciales proporcionadas son incorrectas.',
            'errors' => [
                'email' => ['Las credenciales no son válidas.']
            ]
        ]);
    }

    /**
     * Test that tokens are properly generated and unique
     */
    public function test_tokens_are_unique_per_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        // First login
        $response1 = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        // Second login
        $response2 = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $token1 = $response1->json('token');
        $token2 = $response2->json('token');

        $this->assertNotEquals($token1, $token2);
        $this->assertNotEmpty($token1);
        $this->assertNotEmpty($token2);
    }
}