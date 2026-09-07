<?php

namespace App\Services\Admin;

use App\Repositories\Admin\ClassTimetableRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class ClassTimetableService
{
    protected $classTimetableRepository;

    /**
     * Create a new class instance.
     */
    public function __construct(ClassTimetableRepository $classTimetableRepository)
    {
        $this->classTimetableRepository = $classTimetableRepository;
    }

    /**
     * Get list of class timetables.
     */
    public function getLists($filters, $page, $perPage, $orderColumn, $orderDirection)
    {
        return $this->classTimetableRepository->getLists($filters, $page, $perPage, $orderColumn, $orderDirection);
    }

    /**
     * Create a new class timetable.
     */
    public function create($data)
    {
        try {
            DB::beginTransaction();

            $data['academic_session_id'] = array_key_first(academic_session_options(1));

            // Create class timetable record
            $classTimetable = $this->classTimetableRepository->create($data);
            
            DB::commit();
            
            return $classTimetable;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * Update a class timetable.
     */
    public function update($id, $data)
    {
        try {
            DB::beginTransaction();
            
            // Update class timetable record
            $classTimetable = $this->classTimetableRepository->update($id, $data);
            
            DB::commit();
            
            return $classTimetable;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * Delete a class timetable.
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            
            // Delete class timetable record
            $classTimetable = $this->classTimetableRepository->delete($id);
            
            DB::commit();
            
            return $classTimetable;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * Change status of a class timetable.
     */
    public function changeStatus($id)
    {
        return $this->classTimetableRepository->changeStatus($id);
    }
}
