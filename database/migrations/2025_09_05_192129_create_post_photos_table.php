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
        Schema::create('post_photos', function (Blueprint $table) {
            $table->id(); // Первичный ключ
            $table->foreignId('post_id')->constrained()->onDelete('cascade'); // ID поста, к которому относится фото
            $table->string('photo_path'); // Путь к файлу фотографии
            $table->boolean('is_main')->default(false); // Является ли главной фотографией поста
            $table->integer('order')->default(0); // Порядок сортировки фотографий
            $table->timestamps(); // created_at, updated_at

            // Индексы для оптимизации
            $table->index('post_id'); // Поиск фотографий поста
            $table->index('is_main'); // Поиск главных фотографий
            $table->index('order'); // Сортировка по порядку
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_photos');
    }
};
