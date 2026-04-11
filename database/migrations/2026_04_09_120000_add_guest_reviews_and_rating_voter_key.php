<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->string('guest_display_name', 100)->nullable()->after('user_id');
            $table->string('status', 20)->default('approved')->after('content');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['post_id', 'status']);
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->string('voter_key', 128)->nullable()->after('user_id');
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement("UPDATE ratings SET voter_key = 'u:' || user_id WHERE voter_key IS NULL AND user_id IS NOT NULL");
        } else {
            DB::statement("UPDATE ratings SET voter_key = CONCAT('u:', user_id) WHERE voter_key IS NULL AND user_id IS NOT NULL");
        }

        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique(['post_id', 'user_id']);
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->unique(['post_id', 'voter_key']);
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->string('voter_key', 128)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        // Не откатываем: возможны гостевые оценки/комментарии с user_id = null
    }
};
