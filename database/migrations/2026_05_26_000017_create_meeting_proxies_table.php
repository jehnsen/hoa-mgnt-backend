<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_proxies', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grantor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('proxy_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // A grantor can only have one active proxy per meeting
            $table->unique(['meeting_id', 'grantor_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_proxies');
    }
};
