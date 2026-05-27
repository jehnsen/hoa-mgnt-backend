<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table): void {
            $table->decimal('estimated_cost', 10, 2)->nullable()->after('resolution_notes');
            $table->decimal('actual_cost', 10, 2)->nullable()->after('estimated_cost');
            $table->json('photos')->nullable()->after('actual_cost');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table): void {
            $table->dropColumn(['estimated_cost', 'actual_cost', 'photos']);
        });
    }
};
