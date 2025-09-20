<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Контроллер панели администратора
 * 
 * Обрабатывает административные функции: статистика, модерация постов,
 * одобрение и отклонение контента.
 */
final class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Главная страница панели администратора - статистика и обзор
     * 
     * @return View
     */
    public function dashboard(): View
    {
        // Общая статистика
        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'draft_posts' => Post::where('status', 'draft')->count(),
            'moderation_posts' => Post::where('status', 'moderation')->count(),
            'total_users' => User::count(),
            'total_categories' => Category::count(),
            'total_comments' => Comment::count(),
            'total_ratings' => Rating::count(),
        ];

        // Статистика по дням (последние 7 дней)
        $postsByDay = Post::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Топ категории по количеству постов
        $topCategories = Category::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit(5)
            ->get();

        // Последние посты на модерации
        $recentModerationPosts = Post::with(['user', 'category'])
            ->where('status', 'moderation')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Последние комментарии
        $recentComments = Comment::with(['user', 'post'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'postsByDay',
            'topCategories',
            'recentModerationPosts',
            'recentComments'
        ));
    }

    /**
     * Список постов на модерации
     * 
     * @param Request $request
     * @return View
     */
    public function moderation(Request $request): View
    {
        $query = Post::with(['user', 'category'])
            ->where('status', 'moderation')
            ->orderBy('created_at', 'desc');

        // Фильтрация по категории
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Поиск по заголовку
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->paginate(15);
        $categories = Category::orderBy('name')->get();

        return view('admin.moderation', compact('posts', 'categories'));
    }

    /**
     * Одобрение поста (изменение статуса на published)
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function approvePost(int $id): RedirectResponse
    {
        $post = Post::findOrFail($id);

        // Проверяем, что пост действительно на модерации
        if ($post->status !== 'moderation') {
            return redirect()
                ->back()
                ->with('error', 'Пост не находится на модерации.');
        }

        $post->update(['status' => 'published']);

        return redirect()
            ->back()
            ->with('success', "Пост '{$post->title}' успешно одобрен и опубликован!");
    }

    /**
     * Отклонение поста (изменение статуса на rejected)
     * 
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function rejectPost(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ], [
            'rejection_reason.required' => 'Укажите причину отклонения поста.',
            'rejection_reason.min' => 'Причина отклонения должна содержать минимум 10 символов.',
            'rejection_reason.max' => 'Причина отклонения не может быть длиннее 1000 символов.',
        ]);

        $post = Post::findOrFail($id);

        // Проверяем, что пост действительно на модерации
        if ($post->status !== Post::STATUS_MODERATION) {
            return redirect()
                ->back()
                ->with('error', 'Пост не находится на модерации.');
        }

        $post->update([
            'status' => Post::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', "Пост '{$post->title}' отклонен. Причина отправлена автору.");
    }
}
