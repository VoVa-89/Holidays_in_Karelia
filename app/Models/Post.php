<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Модель поста/статьи
 * 
 * Основная сущность системы - представляет туристические места,
 * достопримечательности, маршруты и другую информацию о Карелии.
 * Содержит геолокационные данные, рейтинги и медиа-контент.
 * 
 * @property int $id
 * @property int $user_id ID автора поста
 * @property int $category_id ID категории поста
 * @property string $title Заголовок поста
 * @property string $slug Уникальный URL-слаг для поста
 * @property string $description Описание/содержимое поста
 * @property string $address Адрес места
 * @property float $latitude Широта (10 цифр, 8 после запятой)
 * @property float $longitude Долгота (11 цифр, 8 после запятой)
 * @property string $status Статус публикации (draft|moderation|published|rejected)
 * @property float $rating Средний рейтинг (3 цифры, 2 после запятой)
 * @property int $views Количество просмотров
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at Дата мягкого удаления
 * 
 * @property-read User $user Автор поста
 * @property-read Category $category Категория поста
 * @property-read \Illuminate\Database\Eloquent\Collection|PostPhoto[] $photos Фотографии поста
 * @property-read \Illuminate\Database\Eloquent\Collection|Comment[] $comments Комментарии к посту
 * @property-read \Illuminate\Database\Eloquent\Collection|Rating[] $ratings Оценки поста
 */
final class Post extends Model
{
    use HasFactory, SoftDeletes;

    // Константы статусов постов
    public const STATUS_DRAFT = 'draft';
    public const STATUS_MODERATION = 'moderation';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';

    // Массив всех возможных статусов
    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_MODERATION,
        self::STATUS_PUBLISHED,
        self::STATUS_REJECTED,
    ];

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'address',
        'latitude',
        'longitude',
        'status',
        'rating',
        'views',
        'rejection_reason',
        'rejected_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
        'views' => 'integer',
        'rejected_at' => 'datetime',
    ];

    /**
     * Получить автора поста
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить категорию поста
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Получить фотографии поста
     */
    public function photos(): HasMany
    {
        return $this->hasMany(PostPhoto::class);
    }

    /**
     * Получить комментарии к посту
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Получить оценки поста
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Получить основное фото или первое из альбома
     * 
     * Сначала ищет фотографию с флагом is_main = true,
     * если такой нет - возвращает первую по порядку сортировки.
     * 
     * @return PostPhoto|null Основная фотография или null если фотографий нет
     */
    public function getMainPhoto(): ?PostPhoto
    {
        return $this->photos()
            ->where('is_main', true)
            ->first() ?? $this->photos()
            ->orderBy('order')
            ->first();
    }

    /**
     * Получить краткое описание (первые N символов)
     * 
     * Удаляет HTML теги и обрезает текст до указанной длины.
     * По умолчанию возвращает первые 150 символов.
     * 
     * @param int $length Максимальная длина описания
     * @return string Краткое описание
     */
    public function getExcerpt(int $length = 150): string
    {
        return Str::limit(strip_tags($this->description), $length);
    }

    /**
     * Увеличить счетчик просмотров
     * 
     * Атомарно увеличивает поле views на 1.
     * Используется при просмотре поста пользователем.
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
