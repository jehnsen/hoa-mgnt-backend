<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('violations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')
                  ->constrained('properties')
                  ->restrictOnDelete();
            $table->foreignId('reported_by')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->comment('Board member or admin who filed the report');
            $table->string('title', 200);
            $table->text('description');
            $table->enum('status', ['draft', 'issued', 'appealed', 'paid', 'resolved'])
                  ->default('draft')
                  ->index();
            $table->decimal('fine_amount', 12, 2)->unsigned()->default(0.00);
            // Geotagged evidence: stored as JSON array of storage paths + lat/lng metadata
            $table->json('evidence_images')->nullable()
                  ->comment('Array of {path, lat, lng, captured_at} objects');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'status']);
            $table->index(['status', 'issued_at'], 'violations_status_issued_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
