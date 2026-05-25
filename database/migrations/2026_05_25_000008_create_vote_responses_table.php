<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vote_responses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vote_id')->constrained('meeting_votes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('selected_option', 300);
            $table->timestamp('voted_at');
            $table->timestamps();

            $table->unique(['vote_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_responses');
    }
};
