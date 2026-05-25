<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AnnouncementAudience;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'author_id',
        'title',
        'body',
        'audience',
        'is_pinned',
        'published_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'audience'     => AnnouncementAudience::class,
            'is_pinned'    => 'boolean',
            'published_at' => 'datetime',
            'expires_at'   => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->whereNotNull('published_at')
              ->where('published_at', '<=', now())
              ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function scopePinned(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('is_pinned', true);
    }
}
