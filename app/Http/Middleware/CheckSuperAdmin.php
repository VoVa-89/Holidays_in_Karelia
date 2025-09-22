<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для проверки роли супер-администратора
 * 
 * Проверяет, что аутентифицированный пользователь имеет роль 'superadmin'.
 * Если пользователь не аутентифицирован или не является супер-администратором,
 * перенаправляет на главную страницу с сообщением об ошибке.
 */
final class CheckSuperAdmin
{
    /**
     * Обработка входящего запроса
     *
     * @param Request $request Входящий HTTP запрос
     * @param Closure $next Следующий middleware в цепочке
     * @return Response HTTP ответ
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем, что пользователь аутентифицирован
        if (!$request->user()) {
            return redirect()->route('home')->with('error', 'Необходима авторизация для доступа к этой странице.');
        }

        // Проверяем, что пользователь является супер-администратором
        if (!$request->user()->isSuperAdmin()) {
            return redirect()->route('home')->with('error', 'У вас нет прав для доступа к этой странице.');
        }

        return $next($request);
    }
}
