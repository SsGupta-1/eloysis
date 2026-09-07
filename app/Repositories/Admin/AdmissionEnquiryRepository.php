<?php

namespace App\Repositories\Admin;

use App\Models\AdmissionEnquiry;
use App\Repositories\BaseRepository;

class AdmissionEnquiryRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(AdmissionEnquiry $model)
    {
        parent::__construct($model);
    }

    /**
     * Summary of get
     * @param array $filters
     * @param int $length
     * @param int $page
     * @param int|null $orderColumn
     * @param string $orderDirection
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get(array $filters, int $length, int $page, ?int $orderColumn, string $orderDirection)
    {
        try {
            $query = $this->model->newQuery()->with([
                'academicSession:id,name',
                'class:id,class_name',
                'handledBy:id,name'
            ]);

            // Academic session filter
            $query->when(
                !empty($filters['academic_session_id']),
                function ($query) use ($filters) {
                    $query->where('academic_session_id', $filters['academic_session_id']);
                }
            );

            // Class filter
            $query->when(
                !empty($filters['class_id']),
                function ($query) use ($filters) {
                    $query->where('class_id', $filters['class_id']);
                }
            );

            // Status filter
            $query->when(
                !empty($filters['status']),
                function ($query) use ($filters) {
                    $query->where('status', $filters['status']);
                }
            );

            // Source filter
            $query->when(
                !empty($filters['source']),
                function ($query) use ($filters) {
                    $query->where('source', $filters['source']);
                }
            );

            // Handled by filter
            $query->when(
                !empty($filters['handled_by']),
                function ($query) use ($filters) {
                    $query->where('handled_by', $filters['handled_by']);
                }
            );

            // Start date filter
            $query->when(
                !empty($filters['start_date']),
                function ($query) use ($filters) {
                    $query->where('created_at', '>=', $filters['start_date']);
                }
            );

            // End date filter
            $query->when(
                !empty($filters['end_date']),
                function ($query) use ($filters) {
                    $query->where('created_at', '<=', $filters['end_date']);
                }
            );

            // Search filter
            $query->when(
                !empty($filters['search']),
                function ($query) use ($filters) {
                    $query->where(function ($query) use ($filters) {
                        $query->where('name', 'like', "%{$filters['search']}%")
                            ->orWhere('email', 'like', "%{$filters['search']}%")
                            ->orWhere('mobile', 'like', "%{$filters['search']}%")
                            ->orWhere('application_no', 'like', "%{$filters['search']}%")
                            ->orwhere('parents_name', 'like', "%{$filters['search']}%")
                            ->orwhere('parents_phone', 'like', "%{$filters['search']}%");
                    });
                }
            );

            // Handle ordering for DataTables
            $sortableColumns = [
                // Add column mappings here if needed
            ];
            $orderColumnIndex = $orderColumn ?? null;
            if ($orderColumnIndex !== null && isset($sortableColumns[$orderColumnIndex])) {
                $query->orderBy($sortableColumns[$orderColumnIndex], $orderDirection === 'desc' ? 'desc' : 'asc');
            } else {
                $query->latest('id');
            }

            // Handle pagination
            $admissionEnquiries = $query->paginate($length, ['*'], 'page', $page);

            return [
                'draw' => $filters['draw'] ?? 1,
                'recordsTotal' => $admissionEnquiries->total(),
                'recordsFiltered' => $admissionEnquiries->total(),
                'data' => $admissionEnquiries->items(),
            ];

        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function findWithRelations(int $id): ?AdmissionEnquiry
    {
        return $this->model
            ->with([
                'academicSession:id,name',
                'class:id,class_name',
                'handledBy:id,name'
            ])
            ->find($id);
    }
}
