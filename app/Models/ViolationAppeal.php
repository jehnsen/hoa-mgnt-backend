<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViolationAppeal extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'violation_id',
        'appellant_id',
        'notes',
        'evidence_images',
    ];

    protected function casts(): array
    {
        return [
            'evidence_images' => 'array',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function violation(): BelongsTo
    {
        return $this->belongsTo(Violation::class);
    }

    public function appellant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'appellant_id');
    }
}
