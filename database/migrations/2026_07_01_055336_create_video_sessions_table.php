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
        Schema::create('video_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_category_id')
                ->constrained('session_categories')
                ->onDelete('restrict');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('video_url');
            $table->string('image')->nullable();
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_sessions');
    }
};
