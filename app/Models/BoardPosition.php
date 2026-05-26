<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BoardPositionTitle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardPosition extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'position',
        'term_start',
        'term_end',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'position'   => BoardPositionTitle::class,
            'term_start' => 'date',
            'term_end'   => 'date',
            'is_active'  => 'boolean',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
