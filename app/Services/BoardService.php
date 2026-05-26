<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BoardPositionTitle;
use App\Models\BoardPosition;
use App\Repositories\Contracts\BoardPositionRepositoryInterface;
use App\Services\Contracts\BoardServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class BoardService implements BoardServiceInterface
{
    public function __construct(
        private readonly BoardPositionRepositoryInterface $boardRepository,
    ) {}

    public function listActive(): Collection
    {
        return $this->boardRepository->allActive();
    }

    public function findOrFail(string $uuid): BoardPosition
    {
        $position = $this->boardRepository->findByUuid($uuid);

        if ($position === null) {
            throw new NotFoundHttpException("Board position [{$uuid}] not found.");
        }

        return $position;
    }

    public function assign(array $data): BoardPosition
    {
        $title = BoardPositionTitle::from($data['position']);

        // Only one active holder per position (except Director which can have multiples)
        if ($title !== BoardPositionTitle::Director) {
            $existing = $this->boardRepository->findActiveByPosition($title);

            if ($existing !== null) {
                throw new HttpException(409, "Position [{$title->label()}] is already occupied. Vacate it first.");
            }
        }

        return DB::transaction(function () use ($data): BoardPosition {
            return $this->boardRepository->create([
                'user_id'    => $data['user_id'],
                'position'   => $data['position'],
                'term_start' => $data['term_start'],
                'term_end'   => $data['term_end'] ?? null,
                'is_active'  => true,
            ]);
        });
    }

    public function update(BoardPosition $position, array $data): BoardPosition
    {
        return $this->boardRepository->update($position, $data);
    }

    public function vacate(BoardPosition $position): BoardPosition
    {
        return $this->boardRepository->update($position, [
            'is_active' => false,
            'term_end'  => $position->term_end ?? now()->toDateString(),
        ]);
    }

    public function delete(BoardPosition $position): void
    {
        $this->boardRepository->delete($position);
    }
}
