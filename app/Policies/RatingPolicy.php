<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Политика доступа для оценок
 * 
 * Определяет права пользователей на выполнение различных действий
 * с оценками: просмотр, создание, обновление, удаление.
 */
final class RatingPolicy
{
    /**
     * Определить, может ли пользователь просматривать любые модели
     * 
     * Все пользователи могут просматривать оценки к опубликованным постам
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Определить, может ли пользователь просматривать модель
     * 
     * Все пользователи могут просматривать оценки к опубликованным постам
     */
    public function view(User $user, Rating $rating): bool
    {
        return $rating->post->status === 'published';
    }

    /**
     * Определить, может ли пользователь создавать модели
     * 
     * Пользователь может создавать оценки если:
     * - Он аутентифицирован, И
     * - Пост опубликован, И
     * - Он еще не оценивал этот пост
     */
    public function create(User $user, ?Rating $rating = null): bool
    {
        // Если передан рейтинг, проверяем статус поста
        if ($rating && $rating->post) {
            return $rating->post->status === 'published';
        }

        return true;
    }

    /**
     * Определить, может ли пользователь обновлять модель
     * 
     * Пользователь может обновлять оценку если:
     * - Он является автором оценки, ИЛИ
     * - Он является администратором
     */
    public function update(User $user, Rating $rating): bool
    {
        return $user->id === $rating->user_id || $user->isAdmin();
    }

    /**
     * Определить, может ли пользователь удалять модель
     * 
     * Пользователь может удалять оценку если:
     * - Он является автором оценки, ИЛИ
     * - Он является администратором
     */
    public function delete(User $user, Rating $rating): bool
    {
        return $user->id === $rating->user_id || $user->isAdmin();
    }

    /**
     * Определить, может ли пользователь восстанавливать модель
     * 
     * Только администраторы могут восстанавливать удаленные оценки
     */
    public function restore(User $user, Rating $rating): bool
    {
        return $user->isAdmin();
    }

    /**
     * Определить, может ли пользователь окончательно удалять модель
     * 
     * Только администраторы могут окончательно удалять оценки
     */
    public function forceDelete(User $user, Rating $rating): bool
    {
        return $user->isAdmin();
    }
}
