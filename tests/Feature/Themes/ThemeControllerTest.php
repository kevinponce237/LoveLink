<?php

namespace Tests\Feature\Themes;

use App\Models\Theme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ThemeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Create some system themes for testing
        $this->createSystemThemes();
    }

    /**
     * Create system themes for testing
     */
    private function createSystemThemes(): void
    {
        Theme::create([
            'user_id' => null,
            'name' => 'Sistema Elegante',
            'description' => 'Tema del sistema elegante',
            'primary_color' => '#D4AF37',
            'secondary_color' => '#F5F5DC',
            'bg_color' => '#FFFFFF',
            'css_class' => 'system-elegant',
        ]);

        Theme::create([
            'user_id' => null,
            'name' => 'Sistema Romance',
            'description' => 'Tema del sistema romántico',
            'primary_color' => '#FF69B4',
            'secondary_color' => '#FFB6C1',
            'bg_color' => '#FFF0F5',
            'css_class' => 'system-romance',
        ]);
    }

    /**
     * Test user can list available themes - Returns 200 with system + user themes
     */
    public function test_user_can_list_available_themes(): void
    {
        $user = User::factory()->create();

        // Create a user theme
        $userTheme = Theme::create([
            'user_id' => $user->id,
            'name' => 'Mi Tema Personal',
            'description' => 'Tema creado por el usuario',
            'primary_color' => '#FF0000',
            'secondary_color' => '#00FF00',
            'bg_color' => '#0000FF',
            'css_class' => 'user-theme',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/themes');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'themes' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'primary_color',
                    'secondary_color',
                    'bg_color',
                    'css_class',
                    'user_id',
                ],
            ],
        ]);

        // Should include both system themes and user theme
        $themes = $response->json('themes');
        $this->assertCount(3, $themes); // 2 system + 1 user

        // Check that both system themes and user theme are present
        $themeNames = collect($themes)->pluck('name')->toArray();
        $this->assertContains('Sistema Elegante', $themeNames);
        $this->assertContains('Sistema Romance', $themeNames);
        $this->assertContains('Mi Tema Personal', $themeNames);
    }

    /**
     * Test user can create custom theme - Returns 201 with created theme
     */
    public function test_user_can_create_custom_theme(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $themeData = [
            'name' => 'Mi Tema Personalizado',
            'description' => 'Un tema único creado por mí',
            'primary_color' => '#FF5733',
            'secondary_color' => '#FFC300',
            'bg_color' => '#F5F5F5',
            'css_class' => 'custom-theme',
        ];

        $response = $this->postJson('/api/themes', $themeData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'theme' => [
                'id',
                'name',
                'description',
                'primary_color',
                'secondary_color',
                'bg_color',
                'css_class',
                'user_id',
            ],
        ]);

        $this->assertEquals('Mi Tema Personalizado', $response->json('theme.name'));
        $this->assertEquals($user->id, $response->json('theme.user_id'));

        $this->assertDatabaseHas('themes', [
            'name' => 'Mi Tema Personalizado',
            'user_id' => $user->id,
            'primary_color' => '#FF5733',
        ]);
    }

    /**
     * Test user can view theme details - Returns 200 with theme data
     */
    public function test_user_can_view_theme_details(): void
    {
        $user = User::factory()->create();

        // Create a theme for the user
        $theme = Theme::create([
            'user_id' => $user->id,
            'name' => 'Tema de Prueba',
            'description' => 'Descripción de prueba',
            'primary_color' => '#123456',
            'secondary_color' => '#654321',
            'bg_color' => '#FFFFFF',
            'css_class' => 'test-theme',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/themes/{$theme->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'theme' => [
                'id',
                'name',
                'description',
                'primary_color',
                'secondary_color',
                'bg_color',
                'css_class',
                'user_id',
            ],
        ]);

        $this->assertEquals($theme->name, $response->json('theme.name'));
        $this->assertEquals($theme->id, $response->json('theme.id'));
    }

    /**
     * Test user can update own theme - Returns 200 with updated theme
     */
    public function test_user_can_update_own_theme(): void
    {
        $user = User::factory()->create();

        $theme = Theme::create([
            'user_id' => $user->id,
            'name' => 'Tema Original',
            'description' => 'Descripción original',
            'primary_color' => '#FF0000',
            'secondary_color' => '#00FF00',
            'bg_color' => '#0000FF',
            'css_class' => 'original-theme',
        ]);

        Sanctum::actingAs($user);

        $updateData = [
            'name' => 'Tema Actualizado',
            'primary_color' => '#AABBCC',
        ];

        $response = $this->putJson("/api/themes/{$theme->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'theme' => [
                'id',
                'name',
                'primary_color',
            ],
        ]);

        $this->assertEquals('Tema Actualizado', $response->json('theme.name'));
        $this->assertEquals('#AABBCC', $response->json('theme.primary_color'));

        $this->assertDatabaseHas('themes', [
            'id' => $theme->id,
            'name' => 'Tema Actualizado',
            'primary_color' => '#AABBCC',
        ]);
    }

    /**
     * Test user can delete own theme - Returns 200 confirmation
     */
    public function test_user_can_delete_own_theme(): void
    {
        $user = User::factory()->create();

        $theme = Theme::create([
            'user_id' => $user->id,
            'name' => 'Tema a Eliminar',
            'description' => 'Este tema será eliminado',
            'primary_color' => '#FF0000',
            'secondary_color' => '#00FF00',
            'bg_color' => '#0000FF',
            'css_class' => 'delete-theme',
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/themes/{$theme->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Tema eliminado exitosamente.',
        ]);

        $this->assertDatabaseMissing('themes', [
            'id' => $theme->id,
        ]);
    }

    /**
     * Test theme creation requires authentication - Returns 401 Unauthorized
     */
    public function test_theme_creation_requires_authentication(): void
    {
        $themeData = [
            'name' => 'Tema Sin Auth',
            'primary_color' => '#FF0000',
            'secondary_color' => '#00FF00',
            'bg_color' => '#0000FF',
            'css_class' => 'no-auth-theme',
        ];

        $response = $this->postJson('/api/themes', $themeData);

        $response->assertStatus(401);
    }

    /**
     * Test theme creation validates required fields - Returns 422 Validation Errors
     */
    public function test_theme_creation_validates_required_fields(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Test missing required fields
        $response = $this->postJson('/api/themes', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'primary_color', 'secondary_color', 'bg_color', 'css_class']);

        // Test empty name
        $response = $this->postJson('/api/themes', [
            'name' => '',
            'primary_color' => '#FF0000',
            'secondary_color' => '#00FF00',
            'bg_color' => '#0000FF',
            'css_class' => 'test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    /**
     * Test theme creation validates hex color format - Returns 422 Invalid color format
     */
    public function test_theme_creation_validates_hex_color_format(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Test invalid primary color format
        $response = $this->postJson('/api/themes', [
            'name' => 'Test Theme',
            'primary_color' => 'invalid-color',
            'secondary_color' => '#00FF00',
            'bg_color' => '#0000FF',
            'css_class' => 'test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['primary_color']);

        // Test invalid color without #
        $response = $this->postJson('/api/themes', [
            'name' => 'Test Theme',
            'primary_color' => 'FF0000',
            'secondary_color' => '#00FF00',
            'bg_color' => '#0000FF',
            'css_class' => 'test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['primary_color']);

        // Test short color format
        $response = $this->postJson('/api/themes', [
            'name' => 'Test Theme',
            'primary_color' => '#F00',
            'secondary_color' => '#00FF00',
            'bg_color' => '#0000FF',
            'css_class' => 'test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['primary_color']);
    }

    /**
     * Test user cannot update system theme - Returns 403 Forbidden
     */
    public function test_user_cannot_update_system_theme(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Try to update a system theme (user_id = null)
        $systemTheme = Theme::where('user_id', null)->first();

        $response = $this->putJson("/api/themes/{$systemTheme->id}", [
            'name' => 'Intento de Modificación',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'No tienes permisos para modificar este tema.',
        ]);
    }

    /**
     * Test user cannot update other user's theme - Returns 403 Forbidden
     */
    public function test_user_cannot_update_other_user_theme(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create theme for user1
        $theme = Theme::create([
            'user_id' => $user1->id,
            'name' => 'Tema de Usuario 1',
            'primary_color' => '#FF0000',
            'secondary_color' => '#00FF00',
            'bg_color' => '#0000FF',
            'css_class' => 'user1-theme',
        ]);

        // Try to update it as user2
        Sanctum::actingAs($user2);

        $response = $this->putJson("/api/themes/{$theme->id}", [
            'name' => 'Intento de Modificación',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'No tienes permisos para modificar este tema.',
        ]);
    }

    /**
     * Test theme not found returns 404 - Returns 404 Not Found
     */
    public function test_theme_not_found_returns_404(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $nonExistentId = 99999;

        // Test show
        $response = $this->getJson("/api/themes/{$nonExistentId}");
        $response->assertStatus(404);

        // Test update
        $response = $this->putJson("/api/themes/{$nonExistentId}", [
            'name' => 'Test',
        ]);
        $response->assertStatus(404);

        // Test delete
        $response = $this->deleteJson("/api/themes/{$nonExistentId}");
        $response->assertStatus(404);
    }

    /**
     * Test user can access system themes - System themes are accessible to all users
     */
    public function test_user_can_access_system_themes(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $systemTheme = Theme::where('user_id', null)->first();

        $response = $this->getJson("/api/themes/{$systemTheme->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('theme.id', $systemTheme->id);
        $response->assertJsonPath('theme.user_id', null);
    }

    /**
     * Test user cannot delete system theme - Returns 403 Forbidden
     */
    public function test_user_cannot_delete_system_theme(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $systemTheme = Theme::where('user_id', null)->first();

        $response = $this->deleteJson("/api/themes/{$systemTheme->id}");

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'No tienes permisos para eliminar este tema.',
        ]);

        // Verify theme still exists
        $this->assertDatabaseHas('themes', [
            'id' => $systemTheme->id,
        ]);
    }

    /**
     * Test theme validation accepts optional fields
     */
    public function test_theme_creation_accepts_optional_fields(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create theme without optional fields
        $response = $this->postJson('/api/themes', [
            'name' => 'Minimal Theme',
            'primary_color' => '#FF0000',
            'secondary_color' => '#00FF00',
            'bg_color' => '#0000FF',
            'css_class' => 'minimal',
        ]);

        $response->assertStatus(201);

        // Create theme with optional fields
        $response = $this->postJson('/api/themes', [
            'name' => 'Complete Theme',
            'description' => 'Theme with description',
            'primary_color' => '#AA0000',
            'secondary_color' => '#00AA00',
            'bg_color' => '#0000AA',
            'bg_image_url' => 'https://example.com/image.jpg',
            'css_class' => 'complete',
        ]);

        $response->assertStatus(201);
        $this->assertEquals('Theme with description', $response->json('theme.description'));
        $this->assertEquals('https://example.com/image.jpg', $response->json('theme.bg_image_url'));
    }
}
