<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't enforce FK constraints; only re-constrain on MySQL/Postgres
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('maintenance_requests', function (Blueprint $table): void {
            $table->dropForeign(['vendor_id']);
            $table->foreign('vendor_id')
                  ->references('id')
                  ->on('vendor_profiles')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('maintenance_requests', function (Blueprint $table): void {
            $table->dropForeign(['vendor_id']);
            $table->foreign('vendor_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }
};
