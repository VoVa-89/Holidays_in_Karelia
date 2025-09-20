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
        Schema::create('comments', function (Blueprint $table) {
            $table->id(); // Первичный ключ
            $table->foreignId('post_id')->constrained()->onDelete('cascade'); // ID поста, к которому относится комментарий
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID автора комментария
            $table->text('content'); // Содержимое комментария
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at для мягкого удаления

            // Индексы для оптимизации
            $table->index('post_id'); // Поиск комментариев к посту
            $table->index('user_id'); // Поиск комментариев пользователя
            $table->index('created_at'); // Сортировка по дате создания
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
