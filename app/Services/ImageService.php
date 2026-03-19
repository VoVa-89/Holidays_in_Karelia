<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;

final readonly class ImageService
{
    private const int MAX_WIDTH  = 1920;
    private const int MAX_HEIGHT = 1080;
    private const int QUALITY    = 82;

    /**
     * Оптимизирует изображение: уменьшает до MAX и конвертирует в WebP.
     *
     * @param string $absolutePath  Полный путь к файлу: public_path($relativePath)
     * @param string $relativePath  Путь как будет храниться в БД: uploads/posts/1/file.jpg
     * @return string               Итоговый путь для БД: uploads/posts/1/file.webp
     *                              При ошибке — исходный $relativePath без изменений
     */
    public function optimize(string $absolutePath, string $relativePath): string
    {
        try {
            $webpAbsolute = (string) preg_replace('/\.(jpe?g|png|gif)$/i', '.webp', $absolutePath);
            $webpRelative = (string) preg_replace('/\.(jpe?g|png|gif)$/i', '.webp', $relativePath);

            $image = Image::read($absolutePath);

            // Уменьшаем только если больше максимума, маленькие не трогаем
            if ($image->width() > self::MAX_WIDTH || $image->height() > self::MAX_HEIGHT) {
                $image->scaleDown(self::MAX_WIDTH, self::MAX_HEIGHT);
            }

            $image->toWebp(self::QUALITY)->save($webpAbsolute);

            // Удаляем оригинал только если конвертация прошла успешно
            if ($webpAbsolute !== $absolutePath && file_exists($absolutePath)) {
                unlink($absolutePath);
            }

            return $webpRelative;

        } catch (\Throwable $e) {
            Log::warning('ImageService: не удалось оптимизировать, сохраняем оригинал', [
                'path'  => $relativePath,
                'error' => $e->getMessage(),
            ]);

            // Фоллбэк: возвращаем исходный путь — загрузка не падает
            return $relativePath;
        }
    }
}
