<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BoardPositionTitle;
use App\Enums\NominationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElectionNomination extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'election_id',
        'nominee_id',
        'nominated_by',
        'position_value',
        'candidate_statement',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'position_value' => BoardPositionTitle::class,
            'status'         => NominationStatus::class,
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(BoardElection::class, 'election_id');
    }

    public function nominee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nominee_id');
    }

    public function nominator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nominated_by');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ElectionVote::class, 'nomination_id');
    }
}
