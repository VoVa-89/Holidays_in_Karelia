<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup-unverified {--days=7 : Количество дней для очистки} {--dry-run : Показать что будет удалено без удаления}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаляет неверифицированных пользователей старше указанного количества дней';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("🔍 Поиск неверифицированных пользователей старше {$days} дней (до {$cutoffDate->format('Y-m-d H:i:s')})...");

        // Находим неверифицированных пользователей
        $unverifiedUsers = User::whereNull('email_verified_at')
            ->where('created_at', '<', $cutoffDate)
            ->get();

        if ($unverifiedUsers->isEmpty()) {
            $this->info('✅ Неверифицированных пользователей для удаления не найдено.');
            return 0;
        }

        $this->warn("⚠️  Найдено {$unverifiedUsers->count()} неверифицированных пользователей:");

        // Показываем список пользователей для удаления
        $headers = ['ID', 'Имя', 'Email', 'Дата регистрации', 'Дней назад'];
        $rows = $unverifiedUsers->map(function ($user) use ($cutoffDate) {
            return [
                $user->id,
                $user->name,
                $user->email,
                $user->created_at->format('Y-m-d H:i:s'),
                $user->created_at->diffInDays(now()) . ' дн.'
            ];
        })->toArray();

        $this->table($headers, $rows);

        if ($dryRun) {
            $this->info('🔍 Режим предварительного просмотра (--dry-run). Ничего не удалено.');
            return 0;
        }

        // Подтверждение удаления
        if (!$this->confirm("🚨 Вы уверены, что хотите удалить {$unverifiedUsers->count()} пользователей? Это действие необратимо!")) {
            $this->info('❌ Операция отменена.');
            return 0;
        }

        $deletedCount = 0;
        $errors = [];

        // Удаляем пользователей
        foreach ($unverifiedUsers as $user) {
            try {
                DB::transaction(function () use ($user) {
                    // Удаляем связанные данные
                    $user->comments()->delete();
                    $user->ratings()->delete();
                    
                    // Удаляем посты и их фотографии
                    foreach ($user->posts as $post) {
                        foreach ($post->photos as $photo) {
                            $photoPath = public_path($photo->photo_path);
                            if (file_exists($photoPath)) {
                                unlink($photoPath);
                            }
                        }
                        $post->photos()->delete();
                    }
                    
                    // Удаляем директории с фотографиями
                    $postsDir = public_path('uploads/posts');
                    if (is_dir($postsDir)) {
                        foreach ($user->posts as $post) {
                            $postDir = $postsDir . '/' . $post->id;
                            if (is_dir($postDir)) {
                                $this->deleteDirectory($postDir);
                            }
                        }
                    }
                    
                    $user->posts()->delete();
                    $user->delete();
                });
                
                $deletedCount++;
                $this->line("✅ Удален пользователь: {$user->name} ({$user->email})");
                
            } catch (\Exception $e) {
                $errors[] = "Ошибка при удалении пользователя {$user->name} ({$user->email}): " . $e->getMessage();
                $this->error("❌ Ошибка при удалении пользователя {$user->name}: " . $e->getMessage());
            }
        }

        // Логируем результат
        Log::info('Unverified users cleanup completed', [
            'deleted_count' => $deletedCount,
            'errors_count' => count($errors),
            'days_threshold' => $days,
            'errors' => $errors
        ]);

        $this->info("🎉 Очистка завершена!");
        $this->info("✅ Удалено пользователей: {$deletedCount}");
        
        if (!empty($errors)) {
            $this->warn("⚠️  Ошибок: " . count($errors));
            foreach ($errors as $error) {
                $this->error("   - {$error}");
            }
        }

        return 0;
    }

    /**
     * Рекурсивное удаление директории
     */
    private function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        return rmdir($dir);
    }
}
