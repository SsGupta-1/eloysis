<?php

namespace App\Repositories\Admin;

use App\Models\FeeHead;
use Illuminate\Pagination\LengthAwarePaginator;

class FeeHeadRepository
{
    public function getFeeHeads(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ): LengthAwarePaginator {
        $query = FeeHead::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['filter_status']) && $filters['filter_status'] !== '') {
            $query->where('is_active', (bool) $filters['filter_status']);
        }

        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'code',
            3 => 'description',
            4 => 'is_active',
            5 => 'created_at',
        ];

        if ($orderColumn !== null && isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDirection);
        } else {
            $query->latest('id');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): FeeHead
    {
        return FeeHead::create($data);
    }

    public function update(int $id, array $data): FeeHead
    {
        $feeHead = FeeHead::findOrFail($id);
        $feeHead->update($data);

        return $feeHead;
    }

    public function delete(int $id): bool
    {
        $feeHead = FeeHead::findOrFail($id);

        return $feeHead->delete();
    }

    public function changeStatus(int $id): bool
    {
        $feeHead = FeeHead::findOrFail($id);
        $feeHead->is_active = ! $feeHead->is_active;

        return $feeHead->save();
    }
}
