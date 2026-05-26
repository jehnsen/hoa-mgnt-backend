<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('committee_members', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('committee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member');
            $table->date('joined_at');
            $table->date('left_at')->nullable();
            $table->timestamps();

            $table->unique(['committee_id', 'user_id', 'left_at'], 'unique_active_membership');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_members');
    }
};
