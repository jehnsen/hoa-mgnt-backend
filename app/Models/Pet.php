<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PetType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'property_id',
        'registered_by',
        'name',
        'type',
        'breed',
        'color',
        'is_vaccinated',
        'vaccination_record',
        'registration_fee',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type'             => PetType::class,
            'is_vaccinated'    => 'boolean',
            'registration_fee' => 'decimal:2',
            'is_active'        => 'boolean',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function registrant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('is_active', true);
    }
}
