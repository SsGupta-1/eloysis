<?php

namespace App\Repositories\Admin;

use App\Models\FeeStructure;
use Illuminate\Pagination\LengthAwarePaginator;

class FeeStructureRepository
{
    public function getFeeStructures(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ): LengthAwarePaginator {
        $query = FeeStructure::with(['academicSession', 'academicClass', 'feeHead', 'feeGroup']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('feeHead', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('academicClass', function ($q) use ($search) {
                $q->where('class_name', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['academic_session_id'])) {
            $query->where('academic_session_id', $filters['academic_session_id']);
        }

        if (! empty($filters['academic_class_id'])) {
            $query->where('academic_class_id', $filters['academic_class_id']);
        }

        if (! empty($filters['fee_head_id'])) {
            $query->where('fee_head_id', $filters['fee_head_id']);
        }

        if (! empty($filters['frequency'])) {
            $query->where('frequency', $filters['frequency']);
        }

        if (isset($filters['filter_status']) && $filters['filter_status'] !== '') {
            $query->where('is_active', (bool) $filters['filter_status']);
        }

        $columns = [
            0 => 'id',
            1 => 'academic_session_id',
            2 => 'academic_class_id',
            3 => 'fee_head_id',
            4 => 'amount',
            5 => 'frequency',
            6 => 'due_date',
            7 => 'is_active',
        ];

        if ($orderColumn !== null && isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDirection);
        } else {
            $query->latest('id');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): FeeStructure
    {
        return FeeStructure::create($data);
    }

    public function update(int $id, array $data): FeeStructure
    {
        $structure = FeeStructure::findOrFail($id);
        $structure->update($data);

        return $structure;
    }

    public function delete(int $id): bool
    {
        $structure = FeeStructure::findOrFail($id);

        return $structure->delete();
    }

    public function changeStatus(int $id): bool
    {
        $structure = FeeStructure::findOrFail($id);
        $structure->is_active = ! $structure->is_active;

        return $structure->save();
    }
}
