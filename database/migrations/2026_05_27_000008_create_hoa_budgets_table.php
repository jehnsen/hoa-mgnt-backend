<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoa_budgets', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedSmallInteger('fiscal_year');
            $table->string('category', 50);
            $table->decimal('budgeted_amount', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['fiscal_year', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoa_budgets');
    }
};
