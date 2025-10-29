<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Контроллер для управления комментариями
 * 
 * Обрабатывает операции создания и удаления комментариев
 * с применением политик доступа и валидации.
 */
final class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Добавление нового комментария к посту
     * 
     * @param Request $request
     * @param string $postSlug
     * @return JsonResponse|RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request, string $postSlug): JsonResponse|RedirectResponse
    {
        $post = Post::where('slug', $postSlug)->firstOrFail();

        // Проверяем, что email подтвержден
        if (!Auth::user()->hasVerifiedEmail()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Для добавления комментариев необходимо подтвердить email. Проверьте почту или запросите повторную отправку письма в профиле.'
                ], 403);
            }

            return redirect()
                ->route('verification.notice')
                ->with('warning', 'Для добавления комментариев необходимо подтвердить email. Проверьте почту или запросите повторную отправку письма в профиле.');
        }

        // Проверяем, что пост опубликован
        if ($post->status !== 'published') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя комментировать неопубликованные посты.'
                ], 403);
            }

            return redirect()
                ->back()
                ->with('error', 'Нельзя комментировать неопубликованные посты.');
        }

        try {
            $validated = $request->validate([
                'content' => [
                    'required',
                    'string',
                    'min:3',
                    'max:1000',
                    function ($attribute, $value, $fail) {
                        // Проверка на спам - слишком много повторяющихся символов
                        if (preg_match('/(.)\1{4,}/', $value)) {
                            $fail('Комментарий содержит слишком много повторяющихся символов.');
                        }

                        // Проверка на минимальное количество слов (улучшенная для русского языка)
                        $wordCount = preg_match_all('/\p{L}+/u', $value);
                        if ($wordCount < 2) {
                            $fail('Комментарий должен содержать минимум 2 слова.');
                        }
                    },
                ],
            ], [
                'content.required' => 'Содержимое комментария обязательно для заполнения.',
                'content.min' => 'Комментарий должен содержать минимум 3 символа.',
                'content.max' => 'Комментарий не может содержать более 1000 символов.',
            ]);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Проверяем права на создание комментария
        try {
            $this->authorize('create', [Comment::class, $post]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав на создание комментариев.'
                ], 403);
            }
            throw $e;
        }

        // Создаем комментарий
        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Комментарий успешно добавлен!',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_name' => $comment->user->name,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'created_at_formatted' => $comment->created_at->format('d.m.Y в H:i')
                ]
            ]);
        }

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success', 'Комментарий успешно добавлен!')
            ->with('scroll_to_comment', $comment->id);
    }

    /**
     * Удаление комментария (мягкое удаление)
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $comment = Comment::with('post')->findOrFail($id);

        // Проверяем права на удаление комментария
        $this->authorize('delete', $comment);

        $postSlug = $comment->post->slug;
        $comment->delete();

        return redirect()
            ->route('posts.show', $postSlug)
            ->with('success', 'Комментарий успешно удален!');
    }
}
