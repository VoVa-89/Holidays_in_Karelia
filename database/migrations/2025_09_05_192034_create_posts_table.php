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
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // Первичный ключ
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID автора поста
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // ID категории поста
            $table->string('title'); // Заголовок поста
            $table->string('slug')->unique(); // Уникальный URL-слаг для поста
            $table->text('description'); // Описание/содержимое поста
            $table->string('address'); // Адрес места
            $table->decimal('latitude', 10, 8); // Широта (10 цифр, 8 после запятой)
            $table->decimal('longitude', 11, 8); // Долгота (11 цифр, 8 после запятой)
            $table->enum('status', ['draft', 'moderation', 'published'])->default('draft'); // Статус публикации
            $table->decimal('rating', 3, 2)->default(0); // Средний рейтинг (3 цифры, 2 после запятой)
            $table->integer('views')->default(0); // Количество просмотров
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at для мягкого удаления

            // Индексы для часто используемых полей
            $table->index('user_id'); // Поиск постов пользователя
            $table->index('category_id'); // Фильтрация по категориям
            $table->index('status'); // Фильтрация по статусу
            $table->index('rating'); // Сортировка по рейтингу
            $table->index('views'); // Сортировка по популярности
            $table->index(['latitude', 'longitude']); // Геопоиск
            $table->index('created_at'); // Сортировка по дате создания
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
