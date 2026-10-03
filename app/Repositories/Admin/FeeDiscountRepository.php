<?php

namespace App\Repositories\Admin;

use App\Models\FeeDiscount;
use Illuminate\Pagination\LengthAwarePaginator;

class FeeDiscountRepository
{
    public function getFeeDiscounts(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ): LengthAwarePaginator {
        $query = FeeDiscount::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['discount_type'])) {
            $query->where('discount_type', $filters['discount_type']);
        }

        if (isset($filters['filter_status']) && $filters['filter_status'] !== '') {
            $query->where('is_active', (bool) $filters['filter_status']);
        }

        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'code',
            3 => 'discount_type',
            4 => 'amount',
            5 => 'is_active',
            6 => 'created_at',
        ];

        if ($orderColumn !== null && isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDirection);
        } else {
            $query->latest('id');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): FeeDiscount
    {
        return FeeDiscount::create($data);
    }

    public function update(int $id, array $data): FeeDiscount
    {
        $discount = FeeDiscount::findOrFail($id);
        $discount->update($data);

        return $discount;
    }

    public function delete(int $id): bool
    {
        $discount = FeeDiscount::findOrFail($id);

        return $discount->delete();
    }

    public function changeStatus(int $id): bool
    {
        $discount = FeeDiscount::findOrFail($id);
        $discount->is_active = ! $discount->is_active;

        return $discount->save();
    }
}
