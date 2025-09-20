<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Контроллер для управления оценками постов
 * 
 * Обрабатывает операции добавления и обновления оценок
 * с проверкой уникальности и применением политик доступа.
 */
final class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Добавление или обновление оценки поста
     * 
     * Пользователь может оценить пост только один раз.
     * При повторной оценке - обновляется существующая.
     * 
     * @param Request $request
     * @param string $postSlug
     * @return JsonResponse|RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request, string $postSlug): JsonResponse|RedirectResponse
    {
        $post = Post::where('slug', $postSlug)->firstOrFail();

        // Дополнительная проверка аутентификации
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходима авторизация для оценки постов.'
                ], 401);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Необходима авторизация для оценки постов.');
        }

        // Проверяем, что пост опубликован
        if ($post->status !== 'published') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя оценивать неопубликованные посты.'
                ], 403);
            }

            return redirect()
                ->back()
                ->with('error', 'Нельзя оценивать неопубликованные посты.');
        }

        $validated = $request->validate([
            'value' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
        ], [
            'value.required' => 'Оценка обязательна для заполнения.',
            'value.integer' => 'Оценка должна быть целым числом.',
            'value.min' => 'Оценка не может быть меньше 1.',
            'value.max' => 'Оценка не может быть больше 5.',
        ]);

        $userId = Auth::id();
        $ratingValue = (int) $validated['value'];

        Log::info('Rating submission attempt', [
            'post_id' => $post->id,
            'user_id' => $userId,
            'rating_value' => $ratingValue,
            'post_slug' => $postSlug
        ]);

        // Проверяем права на создание/обновление оценки
        $this->authorize('create', Rating::class);

        // Ищем существующую оценку пользователя для этого поста
        $existingRating = Rating::where('post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        // Используем транзакцию для атомарности операции
        $isUpdate = $existingRating !== null;

        try {
            DB::transaction(function () use ($post, $userId, $ratingValue, $existingRating) {
                if ($existingRating) {
                    // Обновляем существующую оценку
                    $this->authorize('update', $existingRating);
                    $existingRating->update(['value' => $ratingValue]);
                    Log::info('Rating updated', ['rating_id' => $existingRating->id, 'new_value' => $ratingValue]);
                } else {
                    // Создаем новую оценку
                    $newRating = Rating::create([
                        'post_id' => $post->id,
                        'user_id' => $userId,
                        'value' => $ratingValue,
                    ]);
                    Log::info('Rating created', ['rating_id' => $newRating->id, 'value' => $ratingValue]);
                }

                // Пересчитываем средний рейтинг поста
                $this->updatePostRating($post);
            });
        } catch (\Exception $e) {
            Log::error('Rating transaction failed', [
                'error' => $e->getMessage(),
                'post_id' => $post->id,
                'user_id' => $userId,
                'rating_value' => $ratingValue
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при сохранении оценки: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Ошибка при сохранении оценки.');
        }

        $action = $isUpdate ? 'обновлена' : 'добавлена';
        $totalVotes = Rating::where('post_id', $post->id)->count();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Ваша оценка успешно {$action}!",
                'rating' => $post->fresh()->rating,
                'totalVotes' => $totalVotes
            ]);
        }

        return redirect()
            ->route('posts.show', $post->slug)
            ->with('success', "Ваша оценка успешно {$action}!");
    }

    /**
     * Пересчет среднего рейтинга поста
     * 
     * @param Post $post
     * @return void
     */
    private function updatePostRating(Post $post): void
    {
        $averageRating = Rating::where('post_id', $post->id)
            ->avg('value');

        Log::info('Updating post rating', [
            'post_id' => $post->id,
            'raw_average' => $averageRating,
            'average_type' => gettype($averageRating)
        ]);

        // Преобразуем в число и обрабатываем случай null
        $averageRating = $averageRating ? (float) $averageRating : 0.0;

        $post->update([
            'rating' => round($averageRating, 2)
        ]);

        Log::info('Post rating updated', [
            'post_id' => $post->id,
            'new_rating' => round($averageRating, 2)
        ]);
    }
}
