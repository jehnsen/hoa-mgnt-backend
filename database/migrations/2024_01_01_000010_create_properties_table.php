<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table): void {
            $table->id();
            $table->string('unit_number', 20)->unique();
            $table->string('block', 20)->nullable();
            $table->tinyInteger('floor')->unsigned()->nullable();
            $table->string('street_address', 255);
            // Possible values: condo_unit, townhouse, single_family, commercial
            $table->enum('type', ['condo_unit', 'townhouse', 'single_family', 'commercial'])
                  ->default('condo_unit');
            $table->decimal('monthly_dues', 10, 2)->unsigned()->default(0.00);
            $table->decimal('late_fee_rate', 5, 4)->unsigned()->default(0.0200)
                  ->comment('Fractional rate applied per overdue period, e.g. 0.02 = 2%');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['block', 'floor']);
            $table->index(['is_active', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
