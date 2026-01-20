<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Устанавливаем для всех существующих постов флаг "личные фотографии"
        DB::table('posts')->update([
            'is_personal_photos' => true,
            'photo_source' => null
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Откат не требуется, так как это миграция данных
    }
};
