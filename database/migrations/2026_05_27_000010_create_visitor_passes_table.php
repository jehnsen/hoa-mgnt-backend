<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_passes', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resident_id')->constrained('users')->cascadeOnDelete();
            $table->string('visitor_name');
            $table->string('vehicle_plate', 20)->nullable();
            $table->dateTime('expected_at');
            $table->dateTime('expires_at');
            $table->string('purpose')->nullable();
            $table->string('access_code', 10)->unique();
            $table->boolean('is_used')->default(false);
            $table->dateTime('used_at')->nullable();
            $table->timestamps();

            $table->index(['property_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_passes');
    }
};
