<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('amenities', function (Blueprint $table): void {
            $table->decimal('fee_per_hour', 8, 2)->nullable()->after('capacity');
            $table->decimal('security_deposit', 8, 2)->nullable()->after('fee_per_hour');
            $table->unsignedTinyInteger('monthly_booking_limit')->nullable()->after('security_deposit');
        });
    }

    public function down(): void
    {
        Schema::table('amenities', function (Blueprint $table): void {
            $table->dropColumn(['fee_per_hour', 'security_deposit', 'monthly_booking_limit']);
        });
    }
};
