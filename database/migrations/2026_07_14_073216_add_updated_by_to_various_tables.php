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
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable()->after('author_id')->constrained('users')->onDelete('set null');
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable()->after('author_id')->constrained('users')->onDelete('set null');
        });

        Schema::table('printables', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable()->after('description')->constrained('users')->onDelete('set null');
        });

        Schema::table('video_sessions', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable()->after('description')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
        });

        Schema::table('printables', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
        });

        Schema::table('video_sessions', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
        });
    }
};
