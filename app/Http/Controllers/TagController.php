<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TagController extends Controller
{
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

