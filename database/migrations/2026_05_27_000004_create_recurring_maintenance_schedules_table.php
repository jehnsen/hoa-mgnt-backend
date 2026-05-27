<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_maintenance_schedules', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category');
            $table->string('title', 200);
            $table->text('description');
            $table->string('priority')->default('normal');
            $table->string('frequency');
            $table->unsignedTinyInteger('frequency_interval')->default(1);
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->date('next_due_at');
            $table->date('last_run_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Add recurring_schedule_id FK to maintenance_requests
        Schema::table('maintenance_requests', function (Blueprint $table): void {
            $table->foreignId('recurring_schedule_id')
                  ->nullable()
                  ->after('vendor_id')
                  ->constrained('recurring_maintenance_schedules')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('recurring_schedule_id');
        });
        Schema::dropIfExists('recurring_maintenance_schedules');
    }
};
