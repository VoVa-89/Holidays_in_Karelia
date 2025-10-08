<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Модель пользователя системы
 * 
 * Отвечает за аутентификацию и авторизацию пользователей,
 * управление их ролями и связями с другими сущностями системы.
 * 
 * @property int $id
 * @property string $name Имя пользователя
 * @property string $email Email пользователя (уникальный)
 * @property string $password Хешированный пароль
 * @property string $role Роль пользователя (user|admin|superadmin)
 * @property \Carbon\Carbon|null $email_verified_at Дата подтверждения email
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|Post[] $posts Посты пользователя
 * @property-read \Illuminate\Database\Eloquent\Collection|Comment[] $comments Комментарии пользователя
 * @property-read \Illuminate\Database\Eloquent\Collection|Rating[] $ratings Оценки пользователя
 */
final class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_notifications',
        'public_profile',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'email_notifications' => 'boolean',
        'public_profile' => 'boolean',
    ];

    /**
     * Получить посты пользователя
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Получить комментарии пользователя
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Получить оценки пользователя
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Проверить, является ли пользователь администратором
     * 
     * @return bool true если роль пользователя 'admin' или 'superadmin'
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    /**
     * Проверить, является ли пользователь супер-администратором
     * 
     * @return bool true если роль пользователя 'superadmin'
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Проверить, может ли пользователь редактировать пост
     * 
     * Пользователь может редактировать пост если:
     * - Он является администратором, ИЛИ
     * - Он является автором поста
     * 
     * @param Post $post Пост для проверки
     * @return bool true если пользователь может редактировать пост
     */
    public function canEditPost(Post $post): bool
    {
        return $this->isAdmin() || $this->id === $post->user_id;
    }

}
