<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Провайдер аутентификации и авторизации
 * 
 * Регистрирует политики доступа для моделей приложения
 */
final class AuthServiceProvider extends ServiceProvider
{
    /**
     * Сопоставление моделей с политиками
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    /**
     * Регистрация сервисов аутентификации и авторизации
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
