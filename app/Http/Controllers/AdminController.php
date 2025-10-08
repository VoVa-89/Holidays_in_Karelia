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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response as HttpResponse;
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
            'rejected_posts' => Post::where('status', 'rejected')->count(),
            'total_users' => User::count(),
            'active_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'total_categories' => Category::count(),
            'total_comments' => Comment::count(),
            'total_ratings' => Rating::count(),
            'average_rating' => Rating::avg('value') ?? 0,
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

        // Активные пользователи с их статистикой
        $activeUsers = User::withCount(['posts', 'comments', 'ratings'])
            ->with(['posts' => function($query) {
                $query->where('status', 'published')->latest()->limit(3);
            }])
            ->orderBy('posts_count', 'desc')
            ->limit(10)
            ->get();

        // Временные аккаунты (неверифицированные пользователи)
        $tempUsers = User::whereNull('email_verified_at')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($user) {
                $daysSinceRegistration = $user->created_at->diffInDays(now());
                $daysUntilDeletion = 7 - $daysSinceRegistration;
                $deletionDate = $user->created_at->addDays(7);
                
                return (object) [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                    'days_since_registration' => $daysSinceRegistration,
                    'days_until_deletion' => max(0, $daysUntilDeletion),
                    'deletion_date' => $deletionDate,
                    'is_expired' => $daysUntilDeletion <= 0,
                ];
            });

        // Топ пользователи по рейтингам
        $topRatedUsers = User::withCount(['ratings'])
            ->withAvg('ratings', 'value')
            ->having('ratings_avg_value', '>', 0)
            ->orderBy('ratings_avg_value', 'desc')
            ->limit(5)
            ->get();

        // Последние рейтинги и отзывы
        $recentRatings = Rating::with(['user', 'post'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Статистика по статусам постов
        $postsByStatus = Post::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return view('admin.dashboard', compact(
            'stats',
            'postsByDay',
            'topCategories',
            'recentModerationPosts',
            'recentComments',
            'activeUsers',
            'tempUsers',
            'topRatedUsers',
            'recentRatings',
            'postsByStatus'
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

    /**
     * Управление пользователями (только для супер-администраторов)
     * 
     * @param Request $request
     * @return View
     */
    public function users(Request $request): View
    {
        // Проверяем, что пользователь является супер-администратором
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'У вас нет прав для доступа к этой странице.');
        }

        $query = User::orderBy('created_at', 'desc');

        // Фильтрация по роли
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Поиск по имени или email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(15);
        $roles = ['user', 'admin', 'superadmin'];

        return view('admin.users', compact('users', 'roles'));
    }

    /**
     * Изменение роли пользователя (только для супер-администраторов)
     * 
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function updateUserRole(Request $request, int $id): RedirectResponse
    {
        // Проверяем, что пользователь является супер-администратором
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'У вас нет прав для выполнения этого действия.');
        }

        $request->validate([
            'role' => 'required|in:user,admin,superadmin',
        ], [
            'role.required' => 'Выберите роль пользователя.',
            'role.in' => 'Выбранная роль недопустима.',
        ]);

        $user = User::findOrFail($id);

        // Нельзя изменить роль самому себе
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'Вы не можете изменить свою собственную роль.');
        }

        $oldRole = $user->role;
        $user->update(['role' => $request->role]);

        $roleNames = [
            'user' => 'пользователь',
            'admin' => 'администратор',
            'superadmin' => 'супер-администратор'
        ];

        return redirect()
            ->back()
            ->with('success', "Роль пользователя '{$user->name}' изменена с '{$roleNames[$oldRole]}' на '{$roleNames[$request->role]}'.");
    }

    /**
     * Просмотр постов конкретного пользователя
     * 
     * @param int $id
     * @return View
     */
    public function userPosts(int $id): View
    {
        $user = User::findOrFail($id);
        
        $posts = Post::with(['category', 'photos'])
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.user-posts', compact('user', 'posts'));
    }

    /**
     * Список всех пользователей (для всех администраторов)
     * 
     * @param Request $request
     * @return View
     */
    public function usersList(Request $request): View
    {
        $query = User::withCount(['posts', 'comments', 'ratings'])
            ->orderBy('created_at', 'desc');

        // Поиск по имени или email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Фильтрация по роли (только для супер-администраторов)
        if ($request->filled('role') && auth()->user()->isSuperAdmin()) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15);

        return view('admin.users-list', compact('users'));
    }

    /**
     * Просмотр логов приложения (для админов)
     */
    public function logs(Request $request): View
    {
        $dir = storage_path('logs');
        $logFiles = collect(glob($dir . '/*.log'))
            ->map(fn($p) => [
                'path' => $p,
                'name' => basename($p),
                'mtime' => filemtime($p),
                'size' => filesize($p),
            ])
            ->sortByDesc('mtime')
            ->values();

        $selected = $request->string('file')->toString() ?: ($logFiles->first()['name'] ?? null);

        $tail = (int) $request->get('tail', 500);
        $tail = in_array($tail, [200,500,1000,2000], true) ? $tail : 500;
        $q = trim((string) $request->get('q', ''));
        $level = strtolower((string) $request->get('level', ''));

        $lines = [];
        if ($selected) {
            $path = $dir . DIRECTORY_SEPARATOR . $selected;
            if (!file_exists($path)) {
                abort(404);
            }
            $raw = $this->tailFile($path, $tail);
            $lines = collect($raw)
                ->map(function ($line) {
                    $lvl = '';
                    if (preg_match('/\.(EMERGENCY|ALERT|CRITICAL|ERROR|WARNING|NOTICE|INFO|DEBUG)\:/i', $line, $m)) {
                        $lvl = strtolower($m[1]);
                    }
                    return ['text' => $line, 'level' => $lvl];
                })
                ->when($level !== '', fn($c) => $c->filter(fn($l) => $l['level'] === $level))
                ->when($q !== '', fn($c) => $c->filter(fn($l) => mb_stripos($l['text'], $q) !== false))
                ->values()
                ->all();
        }

        return view('admin.logs', [
            'files' => $logFiles,
            'selectedFile' => $selected,
            'lines' => $lines,
            'q' => $q,
            'level' => $level,
            'tail' => $tail,
        ]);
    }

    /**
     * Скачать текущий лог-файл
     */
    public function downloadLog(Request $request)
    {
        $dir = storage_path('logs');
        $name = $request->string('file')->toString();
        $path = realpath($dir . DIRECTORY_SEPARATOR . $name);
        if (!$name || !$path || !str_starts_with($path, realpath($dir))) {
            abort(404);
        }
        return Response::download($path, $name);
    }

    /**
     * Подтвердить верификацию временного аккаунта
     */
    public function verifyTempUser(int $id): RedirectResponse
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'У вас нет прав для выполнения этого действия.');
        }

        $user = User::findOrFail($id);

        // Проверяем, что это действительно неверифицированный пользователь
        if ($user->email_verified_at !== null) {
            return redirect()->back()->with('error', 'Этот пользователь уже верифицирован.');
        }

        try {
            // Подтверждаем email вручную
            $user->email_verified_at = now();
            $user->save();

            Log::info('Temporary user verified manually by admin', [
                'admin_id' => auth()->id(),
                'verified_user_id' => $id,
                'verified_user_email' => $user->email,
            ]);

            return redirect()->back()->with('success', "Email пользователя '{$user->name}' успешно подтвержден. Теперь он может создавать посты.");
        } catch (\Exception $e) {
            Log::error('Failed to verify temporary user', [
                'admin_id' => auth()->id(),
                'user_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Ошибка при подтверждении email: ' . $e->getMessage());
        }
    }

    /**
     * Удалить временный аккаунт
     */
    public function deleteTempUser(int $id): RedirectResponse
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'У вас нет прав для выполнения этого действия.');
        }

        $user = User::findOrFail($id);

        // Проверяем, что это действительно неверифицированный пользователь
        if ($user->email_verified_at !== null) {
            return redirect()->back()->with('error', 'Этот пользователь уже верифицирован.');
        }

        try {
            DB::transaction(function () use ($user) {
                // Удаляем связанные данные
                $user->comments()->delete();
                $user->ratings()->delete();
                
                // Удаляем посты и их фотографии
                foreach ($user->posts as $post) {
                    foreach ($post->photos as $photo) {
                        $photoPath = public_path($photo->photo_path);
                        if (file_exists($photoPath)) {
                            unlink($photoPath);
                        }
                    }
                    $post->photos()->delete();
                }
                
                // Удаляем директории с фотографиями
                $postsDir = public_path('uploads/posts');
                if (is_dir($postsDir)) {
                    foreach ($user->posts as $post) {
                        $postDir = $postsDir . '/' . $post->id;
                        if (is_dir($postDir)) {
                            $this->deleteDirectory($postDir);
                        }
                    }
                }
                
                $user->posts()->delete();
                $user->delete();
            });

            Log::info('Temporary user deleted by admin', [
                'admin_id' => auth()->id(),
                'deleted_user_id' => $id,
                'deleted_user_email' => $user->email,
            ]);

            return redirect()->back()->with('success', "Временный аккаунт '{$user->name}' успешно удален.");
        } catch (\Exception $e) {
            Log::error('Failed to delete temporary user', [
                'admin_id' => auth()->id(),
                'user_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Ошибка при удалении временного аккаунта: ' . $e->getMessage());
        }
    }

    /**
     * Очистить все логи
     */
    public function clearLogs(Request $request): RedirectResponse
    {
        try {
            $dir = storage_path('logs');
            $files = glob($dir . '/*.log');
            $deletedCount = 0;

            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $deletedCount++;
                }
            }

            Log::info('Logs cleared by admin', [
                'admin_id' => auth()->id(),
                'deleted_files' => $deletedCount,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('admin.logs')->with('success', "Успешно очищено {$deletedCount} файлов логов.");
        } catch (\Exception $e) {
            Log::error('Failed to clear logs', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.logs')->with('error', 'Ошибка при очистке логов: ' . $e->getMessage());
        }
    }

    /**
     * Эффективное чтение последних N строк файла
     */
    private function tailFile(string $path, int $lines = 500): array
    {
        $f = @fopen($path, 'rb');
        if (!$f) return [];
        $buffer = '';
        $pos = -1;
        $lineCount = 0;
        $result = [];
        fseek($f, 0, SEEK_END);
        $fileSize = ftell($f);
        while ($lineCount < $lines && -$pos < $fileSize) {
            fseek($f, $pos, SEEK_END);
            $char = fgetc($f);
            if ($char === "\n") {
                $result[] = strrev($buffer);
                $buffer = '';
                $lineCount++;
            } else {
                $buffer .= $char;
            }
            $pos--;
        }
        if ($buffer !== '') {
            $result[] = strrev($buffer);
        }
        fclose($f);
        return array_reverse(array_filter($result, fn($l) => $l !== null));
    }

    /**
     * Удаление пользователя (только для супер-администраторов)
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteUser(int $id): RedirectResponse
    {
        // Проверяем, что пользователь является супер-администратором
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'У вас нет прав для выполнения этого действия.');
        }

        $user = User::findOrFail($id);

        // Нельзя удалить самого себя
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'Вы не можете удалить свой собственный аккаунт.');
        }

        // Нельзя удалить другого супер-администратора
        if ($user->role === 'superadmin') {
            return redirect()
                ->back()
                ->with('error', 'Нельзя удалить супер-администратора.');
        }

        try {
            DB::transaction(function () use ($user) {
                // Удаляем все фотографии пользователя
                foreach ($user->posts as $post) {
                    foreach ($post->photos as $photo) {
                        $photoPath = public_path($photo->photo_path);
                        if (file_exists($photoPath)) {
                            unlink($photoPath);
                        }
                    }
                }

                // Удаляем директории с фотографиями постов пользователя
                $postsDir = public_path('uploads/posts');
                if (is_dir($postsDir)) {
                    foreach ($user->posts as $post) {
                        $postDir = $postsDir . '/' . $post->id;
                        if (is_dir($postDir)) {
                            $this->deleteDirectory($postDir);
                        }
                    }
                }

                // Удаляем пользователя (каскадное удаление удалит связанные записи)
                $user->delete();
            });

            return redirect()
                ->back()
                ->with('success', "Пользователь '{$user->name}' и все связанные данные успешно удалены.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Произошла ошибка при удалении пользователя: ' . $e->getMessage());
        }
    }

    /**
     * Рекурсивное удаление директории
     * 
     * @param string $dir
     * @return bool
     */
    private function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        return rmdir($dir);
    }
}
