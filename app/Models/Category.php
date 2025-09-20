<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Модель категории постов
 * 
 * Представляет категории для группировки постов по тематикам.
 * Используется для организации контента и навигации по сайту.
 * 
 * @property int $id
 * @property string $name Название категории
 * @property string $slug Уникальный URL-слаг для категории
 * @property string|null $description Описание категории
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|Post[] $posts Посты в этой категории
 */
final class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Получить посты в этой категории
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
