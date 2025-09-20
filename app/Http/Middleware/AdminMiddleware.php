<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для проверки прав администратора
 * 
 * Проверяет, что пользователь аутентифицирован и имеет роль администратора.
 * Если нет - перенаправляет на главную страницу с сообщением об ошибке.
 */
final class AdminMiddleware
{
    /**
     * Обработка входящего запроса
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем, что пользователь аутентифицирован
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Необходимо войти в систему для доступа к панели администратора.');
        }

        // Проверяем, что пользователь является администратором
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'У вас нет прав для доступа к панели администратора.');
        }

        return $next($request);
    }
}
