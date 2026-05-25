<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->string('title', 200);
            $table->text('body');
            $table->enum('audience', ['all', 'residents', 'board_members'])->default('all')->index();
            $table->boolean('is_pinned')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
