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

        // Кастомизация письма подтверждения email
        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function ($notifiable, string $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Подтвердите email')
                ->greeting('Здравствуйте!')
                ->line('Пожалуйста, подтвердите свой email для завершения регистрации на сайте «Отдых в Карелии».')
                ->action('Подтвердить email', $url)
                ->line('Если вы не регистрировались, просто проигнорируйте это письмо.');
        });
    }
}
