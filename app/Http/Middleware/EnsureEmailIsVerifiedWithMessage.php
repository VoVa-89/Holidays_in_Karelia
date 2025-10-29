<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Кастомный middleware, который требует подтвержденный email
 * и показывает понятное сообщение пользователю.
 */
final class EnsureEmailIsVerifiedWithMessage
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            // Для обычных web-запросов показываем понятное сообщение и отправляем на уведомление о верификации
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Для выполнения этого действия необходимо подтвердить ваш email. Мы можем отправить письмо повторно из профиля.'
                ], 403);
            }

            return redirect()
                ->route('verification.notice')
                ->with('warning', 'Подтвердите email, чтобы оставлять комментарии и ставить оценки. Нажмите «Отправить письмо снова» в профиле, если письмо не пришло.');
        }

        return $next($request);
    }
}


