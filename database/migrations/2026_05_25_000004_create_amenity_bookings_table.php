<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amenity_bookings', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('amenity_id')->constrained('amenities')->restrictOnDelete();
            $table->foreignId('property_id')->constrained('properties')->restrictOnDelete();
            $table->foreignId('booked_by')->constrained('users')->restrictOnDelete();
            $table->string('title', 200);
            $table->datetime('start_at');
            $table->datetime('end_at');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending')->index();
            $table->text('notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason', 500)->nullable();
            $table->timestamps();

            $table->index(['amenity_id', 'start_at', 'end_at']);
            $table->index(['property_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenity_bookings');
    }
};
