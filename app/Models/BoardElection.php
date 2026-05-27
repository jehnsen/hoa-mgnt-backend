<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ElectionStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardElection extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'status',
        'nomination_deadline',
        'voting_open_at',
        'voting_close_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status'              => ElectionStatus::class,
            'nomination_deadline' => 'date',
            'voting_open_at'      => 'datetime',
            'voting_close_at'     => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function nominations(): HasMany
    {
        return $this->hasMany(ElectionNomination::class, 'election_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ElectionVote::class, 'election_id');
    }
}
