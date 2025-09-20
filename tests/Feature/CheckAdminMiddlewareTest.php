<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Тесты для CheckAdmin middleware
 */
final class CheckAdminMiddlewareTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Тест: неаутентифицированный пользователь перенаправляется на страницу входа
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Необходимо войти в систему для доступа к этой странице.');
    }

    /**
     * Тест: обычный пользователь перенаправляется на главную страницу
     */
    public function test_regular_user_redirected_to_home(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertRedirect('/');
        $response->assertSessionHas('error', 'У вас нет прав для доступа к этой странице.');
    }

    /**
     * Тест: администратор получает доступ к админ-панели
     */
    public function test_admin_user_can_access_admin_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    /**
     * Тест: API запрос от неаутентифицированного пользователя возвращает JSON ошибку
     */
    public function test_api_unauthenticated_user_returns_json_error(): void
    {
        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Unauthorized',
            'code' => 401
        ]);
        $response->assertJsonStructure([
            'error',
            'message',
            'code'
        ]);
    }

    /**
     * Тест: API запрос от обычного пользователя возвращает JSON ошибку
     */
    public function test_api_regular_user_returns_json_error(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->getJson('/api/admin/users');

        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Forbidden',
            'code' => 403
        ]);
        $response->assertJsonStructure([
            'error',
            'message',
            'code'
        ]);
    }

    /**
     * Тест: API запрос от администратора проходит успешно
     */
    public function test_api_admin_user_can_access_admin_endpoints(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Создаем тестовый маршрут для проверки
        $this->app['router']->get('/api/admin/test', function () {
            return response()->json(['message' => 'Admin access granted']);
        })->middleware(['auth:sanctum', 'check.admin']);

        $response = $this->actingAs($admin)->getJson('/api/admin/test');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Admin access granted']);
    }

    /**
     * Тест: проверка метода isAdmin() в модели User
     */
    public function test_user_is_admin_method(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }

    /**
     * Тест: middleware работает с различными HTTP методами
     */
    public function test_middleware_works_with_different_http_methods(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        // GET запрос
        $response = $this->actingAs($user)->get('/admin');
        $response->assertRedirect('/');

        // POST запрос
        $response = $this->actingAs($user)->post('/admin/posts/1/approve');
        $response->assertRedirect('/');

        // PUT запрос
        $response = $this->actingAs($user)->put('/admin/settings');
        $response->assertRedirect('/');

        // DELETE запрос
        $response = $this->actingAs($user)->delete('/admin/posts/1');
        $response->assertRedirect('/');

        // Администратор должен проходить все проверки
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
    }
}
