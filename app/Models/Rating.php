<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель оценки поста
 * 
 * Представляет оценки пользователей для постов по 5-балльной шкале.
 * Один пользователь может оценить пост только один раз благодаря
 * уникальному индексу на комбинации post_id и user_id.
 * 
 * @property int $id
 * @property int $post_id ID поста, который оценивается
 * @property int $user_id ID пользователя, который ставит оценку
 * @property int $value Значение оценки (1-5)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Post $post Пост, который оценивается
 * @property-read User $user Пользователь, который поставил оценку
 */
final class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'value',
    ];

    protected $casts = [
        'value' => 'integer',
    ];

    /**
     * Получить пост, который оценивается
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Получить пользователя, который поставил оценку
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
