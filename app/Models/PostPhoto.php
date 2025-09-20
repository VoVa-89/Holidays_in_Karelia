<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель фотографии поста
 * 
 * Представляет медиа-файлы (фотографии), прикрепленные к постам.
 * Поддерживает множественные фотографии с возможностью указания
 * главной фотографии и порядка сортировки.
 * 
 * @property int $id
 * @property int $post_id ID поста, к которому относится фотография
 * @property string $photo_path Путь к файлу фотографии
 * @property bool $is_main Является ли главной фотографией поста
 * @property int $order Порядок сортировки фотографий
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Post $post Пост, к которому относится фотография
 */
final class PostPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'photo_path',
        'is_main',
        'order',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Получить пост, к которому относится фотография
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
