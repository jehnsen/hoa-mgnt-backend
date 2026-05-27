<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_meter_readings', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('utility_type', 20);
            $table->string('meter_number', 50)->nullable();
            $table->decimal('previous_reading', 10, 4)->default(0);
            $table->decimal('current_reading', 10, 4);
            $table->decimal('consumption', 10, 4);
            $table->date('reading_date');
            $table->decimal('rate_per_unit', 10, 4);
            $table->decimal('fixed_charge', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('read_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['property_id', 'reading_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_meter_readings');
    }
};
