<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('violations', function (Blueprint $table): void {
            $table->foreignId('invoice_id')
                  ->nullable()
                  ->after('fine_amount')
                  ->constrained('invoices')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('violations', function (Blueprint $table): void {
            $table->dropForeignIdFor(\App\Models\Invoice::class);
            $table->dropColumn('invoice_id');
        });
    }
};
