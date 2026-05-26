<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vote_responses', function (Blueprint $table): void {
            // Tracks who physically cast the vote when a proxy is used.
            // NULL means the voter cast their own vote directly.
            $table->foreignId('cast_by')->nullable()->after('user_id')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vote_responses', function (Blueprint $table): void {
            $table->dropForeign(['cast_by']);
            $table->dropColumn('cast_by');
        });
    }
};
