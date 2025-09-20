<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id(); // Первичный ключ
            $table->foreignId('post_id')->constrained()->onDelete('cascade'); // ID поста, который оценивается
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID пользователя, который ставит оценку
            $table->tinyInteger('value')->unsigned(); // Значение оценки (1-5)
            $table->timestamps(); // created_at, updated_at

            // Уникальный индекс для предотвращения дублирования оценок
            $table->unique(['post_id', 'user_id']); // Один пользователь может оценить пост только один раз

            // Дополнительные индексы для оптимизации
            $table->index('post_id'); // Поиск оценок поста
            $table->index('user_id'); // Поиск оценок пользователя
            $table->index('value'); // Фильтрация по значению оценки
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
