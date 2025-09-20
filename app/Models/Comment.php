<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель комментария
 * 
 * Представляет комментарии пользователей к постам.
 * Поддерживает мягкое удаление для сохранения истории
 * и возможности восстановления комментариев.
 * 
 * @property int $id
 * @property int $post_id ID поста, к которому относится комментарий
 * @property int $user_id ID автора комментария
 * @property string $content Содержимое комментария
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at Дата мягкого удаления
 * 
 * @property-read Post $post Пост, к которому относится комментарий
 * @property-read User $user Автор комментария
 */
final class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'post_id',
        'user_id',
        'content',
    ];

    /**
     * Получить пост, к которому относится комментарий
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Получить автора комментария
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
