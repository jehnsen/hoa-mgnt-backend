<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UtilityType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityMeterReading extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'property_id',
        'utility_type',
        'meter_number',
        'previous_reading',
        'current_reading',
        'consumption',
        'reading_date',
        'rate_per_unit',
        'fixed_charge',
        'notes',
        'read_by',
        'invoice_id',
    ];

    protected function casts(): array
    {
        return [
            'utility_type'     => UtilityType::class,
            'previous_reading' => 'decimal:4',
            'current_reading'  => 'decimal:4',
            'consumption'      => 'decimal:4',
            'reading_date'     => 'date',
            'rate_per_unit'    => 'decimal:4',
            'fixed_charge'     => 'decimal:2',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function totalAmount(): float
    {
        return round((float) $this->consumption * (float) $this->rate_per_unit + (float) $this->fixed_charge, 2);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function reader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
