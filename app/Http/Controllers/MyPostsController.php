<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Контроллер для управления постами пользователя
 * 
 * Позволяет пользователям просматривать свои посты,
 * их статусы и причины отклонения.
 */
final class MyPostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Отображение списка постов пользователя
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Post::with(['category', 'photos'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Фильтрация по статусу
        if ($request->filled('status')) {
            $status = $request->get('status');
            if (in_array($status, ['draft', 'moderation', 'published', 'rejected'])) {
                if ($status === 'rejected') {
                    $query->where('status', 'rejected');
                } else {
                    $query->where('status', $status);
                }
            }
        }

        // Поиск по заголовку
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->paginate(10)->withQueryString();

        // Статистика постов
        $stats = [
            'total' => Post::where('user_id', Auth::id())->count(),
            'published' => Post::where('user_id', Auth::id())->where('status', 'published')->count(),
            'moderation' => Post::where('user_id', Auth::id())->where('status', 'moderation')->count(),
            'rejected' => Post::where('user_id', Auth::id())->where('status', 'rejected')->count(),
            'draft' => Post::where('user_id', Auth::id())->where('status', 'draft')->count(),
        ];

        return view('my-posts.index', compact('posts', 'stats'));
    }
}
