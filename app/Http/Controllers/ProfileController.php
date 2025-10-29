<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Контроллер для управления профилем пользователя
 * 
 * Обрабатывает просмотр и редактирование профиля пользователя,
 * изменение пароля и других настроек аккаунта.
 */
final class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Отображение профиля пользователя
     * 
     * @return View
     */
    public function show(): View
    {
        $user = Auth::user();
        
        // Получаем ID постов пользователя
        $userPostIds = $user->posts()->pluck('id')->toArray();
        
        // Статистика пользователя
        $stats = [
            'posts_count' => $user->posts()->count(),
            'published_posts' => $user->posts()->where('status', 'published')->count(),
            'moderation_posts' => $user->posts()->where('status', 'moderation')->count(),
            'draft_posts' => $user->posts()->where('status', 'draft')->count(),
            'rejected_posts' => $user->posts()->where('status', 'rejected')->count(),
            'comments_count' => $user->comments()->count(), // Комментарии, которые создал пользователь
            'ratings_count' => $user->ratings()->count(), // Оценки, которые поставил пользователь
            'posts_comments_count' => \App\Models\Comment::whereIn('post_id', $userPostIds)->count(), // Комментарии к постам пользователя
            'posts_ratings_count' => \App\Models\Rating::whereIn('post_id', $userPostIds)->count(), // Оценки постов пользователя
        ];

        // Последние посты пользователя
        $recentPosts = $user->posts()
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Комментарии пользователя с привязкой к посту (пагинация)
        $myComments = $user->comments()
            ->with(['post:id,slug,title'])
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'comments_page');

        // Оценки пользователя с привязкой к посту (пагинация)
        $myRatings = $user->ratings()
            ->with(['post:id,slug,title'])
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'ratings_page');

        return view('profile.show', compact('user', 'stats', 'recentPosts', 'myComments', 'myRatings'));
    }

    /**
     * Отображение формы редактирования профиля
     * 
     * @return View
     */
    public function edit(): View
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Обновление профиля пользователя
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ], [
            'name.required' => 'Имя обязательно для заполнения.',
            'name.max' => 'Имя не может быть длиннее 255 символов.',
            'name.unique' => 'Пользователь с таким именем уже существует.',
            'email.required' => 'Email обязателен для заполнения.',
            'email.email' => 'Введите корректный email адрес.',
            'email.unique' => 'Этот email уже используется другим пользователем.',
        ]);

        // Подготовим к обновлению и проверим, менялся ли email
        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $emailChanged = $user->isDirty('email');
        if ($emailChanged) {
            // Сброс подтверждения и отправка нового письма
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged) {
            // Отправляем письмо для подтверждения нового email
            $user->sendEmailVerificationNotification();

            return redirect()
                ->route('verification.notice')
                ->with('info', 'Мы отправили письмо для подтверждения нового email. Проверьте почту.');
        }

        return redirect()
            ->route('profile.show')
            ->with('success', 'Профиль успешно обновлен!');
    }

    /**
     * Отображение формы изменения пароля
     * 
     * @return View
     */
    public function editPassword(): View
    {
        return view('profile.password');
    }

    /**
     * Обновление пароля пользователя
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Введите текущий пароль.',
            'password.required' => 'Введите новый пароль.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.confirmed' => 'Подтверждение пароля не совпадает.',
        ]);

        $user = Auth::user();

        // Проверяем текущий пароль
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()
                ->back()
                ->withErrors(['current_password' => 'Неверный текущий пароль.']);
        }

        // Обновляем пароль
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Пароль успешно изменен!');
    }

    /**
     * Отображение настроек пользователя
     * 
     * @return View
     */
    public function settings(): View
    {
        $user = Auth::user();
        return view('profile.settings', compact('user'));
    }

    /**
     * Обновление настроек пользователя
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'email_notifications' => 'boolean',
            'public_profile' => 'boolean',
        ]);

        // Обновляем настройки
        $user->update([
            'email_notifications' => $request->boolean('email_notifications', true),
            'public_profile' => $request->boolean('public_profile', false),
        ]);

        return redirect()
            ->route('profile.settings')
            ->with('success', 'Настройки успешно обновлены!');
    }

    /**
     * Удаление аккаунта пользователя
     * 
     * @return RedirectResponse
     */
    public function deleteAccount(): RedirectResponse
    {
        $user = Auth::user();

        // Начинаем транзакцию для обеспечения целостности данных
        DB::transaction(function () use ($user) {
            // 1. Удаляем все фотографии постов пользователя
            $posts = $user->posts()->with('photos')->get();
            foreach ($posts as $post) {
                // Удаляем фотографии с сервера
                foreach ($post->photos as $photo) {
                    $photoPath = public_path('storage/' . $photo->path);
                    if (file_exists($photoPath)) {
                        unlink($photoPath);
                    }
                }
                
                // Удаляем папку поста, если она существует
                $postDir = public_path('storage/posts/' . $post->id);
                if (is_dir($postDir)) {
                    rmdir($postDir);
                }
            }

            // 2. Удаляем все связанные данные пользователя
            $user->comments()->delete(); // Комментарии пользователя
            $user->ratings()->delete(); // Оценки пользователя
            
            // 3. Удаляем все посты пользователя (включая фотографии)
            $user->posts()->each(function ($post) {
                $post->photos()->delete(); // Удаляем записи о фотографиях
                $post->forceDelete(); // Принудительно удаляем пост
            });

            // 4. Удаляем самого пользователя
            $user->forceDelete();
        });

        // Выходим из системы
        Auth::logout();

        return redirect()
            ->route('home')
            ->with('success', 'Ваш аккаунт и все связанные данные успешно удалены. Спасибо за использование нашего сайта!');
    }
}
