<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
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
        // Получаем посты с координатами для карты
        $mapPosts = Post::query()
            ->select(['id', 'title', 'slug', 'latitude', 'longitude', 'address', 'rating', 'category_id'])
            ->with('category:id,name')
            ->where('status', 'published')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest('created_at')
            ->limit(500)
            ->get();

        return view('home', compact('mapPosts'));
    }
}
