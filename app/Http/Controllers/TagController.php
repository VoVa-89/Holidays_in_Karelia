<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class TagController extends Controller
{
    /**
     * Страница тега: список опубликованных постов с данным тегом.
     *
     * GET /tags/{slug}
     */
    public function show(string $slug): View
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = Post::with(['user', 'category', 'mainPhoto', 'tags'])
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereHas('tags', fn($q) => $q->where('slug', $slug))
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('tags.show', compact('tag', 'posts'));
    }

    /**
     * AJAX‑подсказки по тегам для Tagify.
     *
     * GET /tags/suggest?q=во
     * Возвращает JSON‑массив имён тегов: ["Водопады", "Водопады Карелии", ...]
     */
    public function suggest(Request $request): JsonResponse
    {
        $q = (string) $request->query('q', '');

        $tags = Tag::query()
            ->when($q !== '', static function ($query) use ($q): void {
                $query->where('name', 'like', '%' . $q . '%');
            })
            ->orderBy('name')
            ->limit(10)
            ->pluck('name')
            ->values();

        return response()->json($tags);
    }
}

