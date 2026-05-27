<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BoardPositionTitle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectionVote extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'election_id',
        'voter_id',
        'nomination_id',
        'position_value',
        'cast_at',
    ];

    protected function casts(): array
    {
        return [
            'position_value' => BoardPositionTitle::class,
            'cast_at'        => 'datetime',
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

    public function voter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voter_id');
    }

    public function nomination(): BelongsTo
    {
        return $this->belongsTo(ElectionNomination::class, 'nomination_id');
    }
}
