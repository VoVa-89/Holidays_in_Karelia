<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Политика доступа для комментариев
 * 
 * Определяет права пользователей на выполнение различных действий
 * с комментариями: просмотр, создание, редактирование, удаление.
 */
final class CommentPolicy
{
    /**
     * Определить, может ли пользователь просматривать любые модели
     * 
     * Все аутентифицированные пользователи могут просматривать комментарии
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Определить, может ли пользователь просматривать модель
     * 
     * Все пользователи могут просматривать комментарии к опубликованным постам
     */
    public function view(User $user, Comment $comment): bool
    {
        return $comment->post->status === 'published';
    }

    /**
     * Определить, может ли пользователь создавать модели
     * 
     * Пользователь может создавать комментарии если:
     * - Он аутентифицирован, И
     * - Пост опубликован
     */
    public function create(User $user, $model = null): bool
    {
        // Если передан пост, проверяем его статус
        if ($model instanceof \App\Models\Post) {
            return $model->status === 'published';
        }

        // Если передан комментарий, проверяем статус поста
        if ($model instanceof Comment && $model->post) {
            return $model->post->status === 'published';
        }

        return true;
    }

    /**
     * Определить, может ли пользователь обновлять модель
     * 
     * Пользователь может редактировать комментарий если:
     * - Он является автором комментария, ИЛИ
     * - Он является администратором
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isAdmin();
    }

    /**
     * Определить, может ли пользователь удалять модель
     * 
     * Пользователь может удалять комментарий если:
     * - Он является автором комментария, ИЛИ
     * - Он является администратором, ИЛИ
     * - Он является автором поста
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id
            || $user->isAdmin()
            || $user->id === $comment->post->user_id;
    }

    /**
     * Определить, может ли пользователь восстанавливать модель
     * 
     * Только администраторы могут восстанавливать удаленные комментарии
     */
    public function restore(User $user, Comment $comment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Определить, может ли пользователь окончательно удалять модель
     * 
     * Только администраторы могут окончательно удалять комментарии
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return $user->isAdmin();
    }
}
