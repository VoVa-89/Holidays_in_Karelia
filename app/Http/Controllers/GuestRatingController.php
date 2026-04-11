<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Rating;
use App\Services\MathCaptcha;
use App\Services\PostRatingService;
use App\Support\GuestVoterKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

final class GuestRatingController extends Controller
{
    public function store(Request $request, string $postSlug, MathCaptcha $mathCaptcha, PostRatingService $postRatingService): JsonResponse
    {
        $post = Post::where('slug', $postSlug)->firstOrFail();

        if ($post->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя оценивать неопубликованные посты.',
            ], 403);
        }

        if (! $mathCaptcha->verify('guest_rating', (string) $request->input('captcha_id', ''), $request->input('captcha_answer'))) {
            throw ValidationException::withMessages([
                'captcha_answer' => ['Неверный ответ или срок проверки истёк. Обновите страницу.'],
            ]);
        }

        $validated = $request->validate([
            'value' => ['required', 'integer', 'min:1', 'max:5'],
        ], [
            'value.required' => 'Оценка обязательна.',
            'value.min' => 'Оценка не может быть меньше 1.',
            'value.max' => 'Оценка не может быть больше 5.',
        ]);

        $ratingValue = (int) $validated['value'];
        $voterKey = GuestVoterKey::forRequest($request, $post);

        $existingRating = Rating::where('post_id', $post->id)
            ->where('voter_key', $voterKey)
            ->first();

        $isUpdate = $existingRating !== null;

        try {
            DB::transaction(function () use ($post, $ratingValue, $existingRating, $voterKey, $postRatingService): void {
                if ($existingRating) {
                    $existingRating->update(['value' => $ratingValue]);
                } else {
                    Rating::create([
                        'post_id' => $post->id,
                        'user_id' => null,
                        'voter_key' => $voterKey,
                        'value' => $ratingValue,
                    ]);
                }

                $postRatingService->recalculateAverage($post->fresh());
            });
        } catch (\Exception $e) {
            Log::error('Guest rating failed', ['error' => $e->getMessage(), 'post_id' => $post->id]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сохранении оценки.',
            ], 500);
        }

        $action = $isUpdate ? 'обновлена' : 'учтена';
        $totalVotes = Rating::where('post_id', $post->id)->count();

        return response()->json([
            'success' => true,
            'message' => "Ваша оценка {$action}!",
            'rating' => $post->fresh()->rating,
            'totalVotes' => $totalVotes,
        ]);
    }
}
