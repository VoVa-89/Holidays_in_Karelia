<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Изменяем ENUM поле status, добавляя значение 'rejected'
        DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'moderation', 'published', 'rejected') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем ENUM поле status к исходному состоянию
        DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'moderation', 'published') DEFAULT 'draft'");
    }
};
