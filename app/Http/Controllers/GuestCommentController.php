<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Services\MathCaptcha;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class GuestCommentController extends Controller
{
    public function store(Request $request, string $postSlug, MathCaptcha $mathCaptcha): JsonResponse
    {
        $post = Post::where('slug', $postSlug)->firstOrFail();

        if ($post->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя комментировать неопубликованные посты.',
            ], 403);
        }

        if (! $mathCaptcha->verify('guest_comment', (string) $request->input('captcha_id', ''), $request->input('captcha_answer'))) {
            throw ValidationException::withMessages([
                'captcha_answer' => ['Неверный ответ или срок проверки истёк. Обновите страницу.'],
            ]);
        }

        $validated = $request->validate([
            'guest_display_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\p{N}\s\-_\.]+$/u'],
            'content' => [
                'required',
                'string',
                'min:3',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if (preg_match('/(.)\1{4,}/', $value)) {
                        $fail('Комментарий содержит слишком много повторяющихся символов.');
                    }
                    $wordCount = preg_match_all('/\p{L}+/u', $value);
                    if ($wordCount < 2) {
                        $fail('Комментарий должен содержать минимум 2 слова.');
                    }
                },
            ],
        ], [
            'guest_display_name.required' => 'Укажите имя или ник.',
            'guest_display_name.regex' => 'Имя содержит недопустимые символы.',
            'content.required' => 'Введите текст отзыва.',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => null,
            'guest_display_name' => $validated['guest_display_name'],
            'content' => $validated['content'],
            'status' => Comment::STATUS_PENDING,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Отзыв отправлен на модерацию. После проверки он появится на странице.',
            'comment_id' => $comment->id,
        ]);
    }
}
