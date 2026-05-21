<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_id')
                  ->constrained('properties')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // Tracks tenancy period; null move_out_at means currently active
            $table->date('move_in_at');
            $table->date('move_out_at')->nullable();
            $table->boolean('is_primary_resident')->default(false);
            $table->timestamps();

            $table->unique(['property_id', 'user_id']);
            $table->index(['user_id', 'move_out_at']);
            $table->index(['property_id', 'is_primary_resident']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_user');
    }
};
