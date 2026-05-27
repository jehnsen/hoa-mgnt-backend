<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\VisitorPass;
use App\Repositories\Contracts\VisitorPassRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class VisitorPassRepository implements VisitorPassRepositoryInterface
{
    public function __construct(private readonly VisitorPass $model) {}

    public function findByUuid(string $uuid): ?VisitorPass
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['property', 'resident'])
                           ->first();
    }

    public function findByAccessCode(string $code): ?VisitorPass
    {
        return $this->model->newQuery()
                           ->where('access_code', $code)
                           ->with(['property', 'resident'])
                           ->first();
    }

    public function forProperty(int $propertyId, bool $activeOnly = false): Collection
    {
        $query = $this->model->newQuery()
                             ->where('property_id', $propertyId)
                             ->with('resident')
                             ->orderByDesc('expected_at');

        if ($activeOnly) {
            $query->where('expires_at', '>=', Carbon::now())
                  ->where('is_used', false);
        }

        return $query->get();
    }

    public function create(array $data): VisitorPass
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(VisitorPass $pass, array $data): VisitorPass
    {
        $pass->fill($data)->save();

        return $pass->refresh();
    }

    public function delete(VisitorPass $pass): void
    {
        $pass->delete();
    }
}
