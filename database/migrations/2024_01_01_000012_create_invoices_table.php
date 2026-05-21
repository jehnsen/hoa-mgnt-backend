<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            // UUIDs as the public-facing identifier; prevents enumeration attacks
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')
                  ->constrained('properties')
                  ->restrictOnDelete();
            $table->string('description', 255);
            $table->decimal('base_amount', 12, 2)->unsigned();
            $table->decimal('late_fee_amount', 12, 2)->unsigned()->default(0.00);
            $table->decimal('total_amount', 12, 2)->unsigned()->storedAs('base_amount + late_fee_amount');
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending')->index();
            $table->date('due_at')->index();
            $table->date('period_month')
                  ->comment('Billing month this invoice covers, e.g. 2024-01-01 for January 2024');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'status']);
            $table->index(['property_id', 'period_month'], 'invoices_property_period_idx');
            $table->index(['status', 'due_at'], 'invoices_status_due_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
