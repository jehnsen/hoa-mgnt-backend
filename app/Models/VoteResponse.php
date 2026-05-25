<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoteResponse extends Model
{
    protected $fillable = [
        'vote_id',
        'user_id',
        'selected_option',
        'voted_at',
    ];

    protected function casts(): array
    {
        return [
            'voted_at' => 'datetime',
        ];
    }

    public function vote(): BelongsTo
    {
        return $this->belongsTo(MeetingVote::class, 'vote_id');
    }

    public function voter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
