<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Контроллер главной страницы
 *
 * Отображает лендинг с картой Карелии и приветствием
 */
final class HomeController extends Controller
{
    /**
     * Главная страница - лендинг с картой
     *
     * @return View
     */
    public function index(): View
    {
        $mapPosts = Cache::remember('home_map_posts', now()->addHours(1), fn () =>
            Post::query()
                ->select(['id', 'title', 'slug', 'latitude', 'longitude', 'address', 'rating', 'category_id'])
                ->with('category:id,name,slug')
                ->where('status', Post::STATUS_PUBLISHED)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->latest('created_at')
                ->limit(500)
                ->get()
        );

        $topAttractions = Cache::remember('home_top_attractions', now()->addHours(3), fn () =>
            Post::query()
                ->select(['id', 'title', 'slug', 'rating', 'views', 'description'])
                ->with(['category:id,name,slug', 'mainPhoto'])
                ->where('status', Post::STATUS_PUBLISHED)
                ->whereHas('category', fn ($q) => $q->where('slug', 'dostoprimechatelnosti'))
                ->orderByDesc('rating')
                ->limit(3)
                ->get()
        );

        $topRestPlaces = Cache::remember('home_top_rest_places', now()->addHours(3), fn () =>
            Post::query()
                ->select(['id', 'title', 'slug', 'rating', 'views', 'description'])
                ->with(['category:id,name,slug', 'mainPhoto'])
                ->where('status', Post::STATUS_PUBLISHED)
                ->whereHas('category', fn ($q) => $q->where('slug', 'mesta-otdykha'))
                ->orderByDesc('rating')
                ->limit(3)
                ->get()
        );

        return view('home', compact('mapPosts', 'topAttractions', 'topRestPlaces'));
    }
}
