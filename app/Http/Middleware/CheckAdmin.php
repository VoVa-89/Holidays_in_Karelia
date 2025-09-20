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
 * Поддерживает различные типы ответов (JSON для API, редирект для web).
 * 
 * @package App\Http\Middleware
 */
final class CheckAdmin
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
            return $this->unauthorizedResponse($request, 'Необходимо войти в систему для доступа к этой странице.');
        }

        // Проверяем, что пользователь является администратором
        if (!auth()->user()->isAdmin()) {
            return $this->forbiddenResponse($request, 'У вас нет прав для доступа к этой странице.');
        }

        return $next($request);
    }

    /**
     * Возвращает ответ для неаутентифицированных пользователей
     *
     * @param Request $request
     * @param string $message
     * @return Response
     */
    private function unauthorizedResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => $message,
                'code' => 401
            ], 401);
        }

        return redirect()->route('login')
            ->with('error', $message);
    }

    /**
     * Возвращает ответ для пользователей без прав администратора
     *
     * @param Request $request
     * @param string $message
     * @return Response
     */
    private function forbiddenResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => $message,
                'code' => 403
            ], 403);
        }

        return redirect()->route('home')
            ->with('error', $message);
    }
}
