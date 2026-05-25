<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VoteStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingVote extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'meeting_id',
        'created_by',
        'question',
        'options',
        'closes_at',
        'is_anonymous',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status'       => VoteStatus::class,
            'options'      => 'array',
            'closes_at'    => 'datetime',
            'is_anonymous' => 'boolean',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(VoteResponse::class, 'vote_id');
    }

    public function isOpen(): bool
    {
        return $this->status === VoteStatus::Open
            && ($this->closes_at === null || $this->closes_at->isFuture());
    }
}
