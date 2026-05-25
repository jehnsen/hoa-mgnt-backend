<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MeetingStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'created_by',
        'title',
        'description',
        'location',
        'scheduled_at',
        'status',
        'agenda',
        'minutes',
    ];

    protected function casts(): array
    {
        return [
            'status'       => MeetingStatus::class,
            'scheduled_at' => 'datetime',
            'agenda'       => 'array',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(MeetingVote::class);
    }

    public function canTransitionTo(MeetingStatus $next): bool
    {
        return $this->status->canTransitionTo($next);
    }
}
