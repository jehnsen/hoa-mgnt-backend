<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'uploaded_by',
        'title',
        'description',
        'category',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'is_public',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'category'     => DocumentCategory::class,
            'file_size'    => 'integer',
            'is_public'    => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopePublic(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('is_public', true)->whereNotNull('published_at');
    }
}
