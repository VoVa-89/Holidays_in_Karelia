<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Поиск опубликованных постов рядом с точкой (MySQL 8+ / MariaDB: ST_Distance_Sphere, SRID 4326).
 * На других драйверах БД возвращает пустую коллекцию (например SQLite в тестах).
 */
final class NearbyPostsService
{
    public const SLUG_ATTRACTIONS = 'dostoprimechatelnosti';

    public const SLUG_REST = 'mesta-otdykha';

    private const SRID_WGS84 = 4326;

    private const RADIUS_KM = 50.0;

    private const DEFAULT_LIMIT = 2;

    /**
     * @return Collection<int, Post> у каждого поста есть динамический атрибут distance_meters
     */
    public function findNearbyPublished(
        Post $origin,
        string $categorySlug,
        ?float $radiusKm = null,
        ?int $limit = null,
    ): Collection {
        if (! $this->supportsSphereDistance()) {
            return collect();
        }

        if ($origin->latitude === null || $origin->longitude === null) {
            return collect();
        }

        $lng = (float) $origin->longitude;
        $lat = (float) $origin->latitude;
        $radiusM = ($radiusKm ?? self::RADIUS_KM) * 1000.0;
        $limit = $limit ?? self::DEFAULT_LIMIT;

        $sphere = 'ST_Distance_Sphere(
            ST_SRID(POINT(posts.longitude, posts.latitude), ' . self::SRID_WGS84 . '),
            ST_SRID(POINT(?, ?), ' . self::SRID_WGS84 . ')
        )';

        $sub = Post::query()
            ->select('posts.*')
            ->selectRaw("{$sphere} AS distance_meters", [$lng, $lat])
            ->join('categories', 'categories.id', '=', 'posts.category_id')
            ->where('categories.slug', $categorySlug)
            ->where('posts.status', Post::STATUS_PUBLISHED)
            ->where('posts.id', '!=', $origin->id)
            ->whereNotNull('posts.latitude')
            ->whereNotNull('posts.longitude');

        // Внешний запрос: без SoftDeletes — иначе scope добавит `posts.deleted_at`, а таблица уже алиас `nearby`
        return Post::query()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->fromSub($sub, 'nearby')
            ->where('nearby.distance_meters', '<=', $radiusM)
            ->orderBy('nearby.distance_meters')
            ->orderByDesc('nearby.rating')
            ->with('mainPhoto')
            ->limit($limit)
            ->get();
    }

    private function supportsSphereDistance(): bool
    {
        return in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true);
    }
}
