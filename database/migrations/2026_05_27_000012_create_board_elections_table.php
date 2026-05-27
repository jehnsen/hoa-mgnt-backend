<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_elections', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 30)->default('draft');
            $table->date('nomination_deadline')->nullable();
            $table->dateTime('voting_open_at')->nullable();
            $table->dateTime('voting_close_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('election_nominations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('election_id')->constrained('board_elections')->cascadeOnDelete();
            $table->foreignId('nominee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('nominated_by')->constrained('users')->cascadeOnDelete();
            $table->string('position_value', 30);
            $table->text('candidate_statement')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();

            $table->unique(['election_id', 'nominee_id', 'position_value'], 'nom_election_nominee_pos_unique');
        });

        Schema::create('election_votes', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('election_id')->constrained('board_elections')->cascadeOnDelete();
            $table->foreignId('voter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('nomination_id')->constrained('election_nominations')->cascadeOnDelete();
            $table->string('position_value', 30);
            $table->dateTime('cast_at');
            $table->timestamps();

            $table->unique(['election_id', 'voter_id', 'position_value'], 'vote_election_voter_pos_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_votes');
        Schema::dropIfExists('election_nominations');
        Schema::dropIfExists('board_elections');
    }
};
