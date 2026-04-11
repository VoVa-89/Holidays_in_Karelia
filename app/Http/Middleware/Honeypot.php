<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Простая honeypot-защита от ботов на формах
 * - Поле-приманка `website` должно быть пустым
 * - Время заполнения формы `form_started_at` должно быть не менее N секунд
 */
final class Honeypot
{
    private int $minSecondsToSubmit;

    public function __construct()
    {
        // Минимальное время (в секундах), за которое человек в среднем заполняет форму
        $this->minSecondsToSubmit = 5;
    }

    public function handle(Request $request, Closure $next): Response
    {
        // 1) Поле-приманка: если не пусто — это бот
        $baitField = (string) $request->input('website', '');
        if ($baitField !== '') {
            return $this->fail($request, 'Подозрительная активность обнаружена.');
        }

        // 2) Проверка времени заполнения формы
        $startedAt = (int) $request->input('form_started_at', 0);
        if ($startedAt > 0) {
            $elapsed = time() - $startedAt;
            if ($elapsed < $this->minSecondsToSubmit) {
                return $this->fail($request, 'Форма отправлена слишком быстро.');
            }
        } else {
            return $this->fail($request, 'Не удалось проверить отправку формы.');
        }

        return $next($request);
    }

    private function fail(Request $request, string $message): RedirectResponse|Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        return redirect()->back()->withInput()->with('error', $message);
    }
}


