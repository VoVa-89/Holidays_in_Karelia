<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

final class PostObserver
{
    public function saved(Post $post): void
    {
        if ($post->isDirty(['status', 'rating', 'category_id'])) {
            $this->clearHomeCache();
        }
    }

    public function deleted(Post $post): void
    {
        $this->clearHomeCache();
    }

    public function restored(Post $post): void
    {
        $this->clearHomeCache();
    }

    private function clearHomeCache(): void
    {
        Cache::forget('home_map_posts');
        Cache::forget('home_top_attractions');
        Cache::forget('home_top_rest_places');
    }
}
