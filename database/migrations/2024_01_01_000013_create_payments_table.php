<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('invoice_id')
                  ->constrained('invoices')
                  ->restrictOnDelete();
            $table->foreignId('received_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Board member or admin who recorded this payment');
            $table->decimal('amount', 12, 2)->unsigned();
            $table->enum('payment_method', ['cash', 'check', 'bank_transfer', 'credit_card', 'online']);
            // Unique reference number from the payment gateway or check number
            $table->string('transaction_reference', 100)->nullable()->unique();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at');
            $table->timestamps();

            $table->index(['invoice_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
