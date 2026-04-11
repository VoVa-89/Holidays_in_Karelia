<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use App\Models\Rating;
use Illuminate\Support\Facades\Log;

final class PostRatingService
{
    public function recalculateAverage(Post $post): void
    {
        $averageRating = Rating::where('post_id', $post->id)->avg('value');

        Log::info('Updating post rating', [
            'post_id' => $post->id,
            'raw_average' => $averageRating,
        ]);

        $averageRating = $averageRating ? (float) $averageRating : 0.0;

        $post->update([
            'rating' => round($averageRating, 2),
        ]);
    }
}
