<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AcademicClassRequest;
use App\Models\AcademicClass;
use App\Services\Admin\AcademicClassService;
use Illuminate\Http\Request;

class AcademicClassController extends BaseController
{
    protected AcademicClassService $academicClassService;

    public function __construct(AcademicClassService $academicClassService)
    {
        $this->academicClassService = $academicClassService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.classes.index');
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'filter_status' => $request->input('filter_status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $classes = $this->academicClassService
            ->getAcademicClasses(
                filters: $filters,
                page: $page,
                perPage: $length,
                orderColumn: $orderColumn !== null ? (int) $orderColumn : null,
                orderDirection: $orderDirection
            );

        return $this->datatable(
            $classes,
            (int) $request->input('draw', 1)
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicClassRequest $request)
    {
        // dd($request->all());
        $this->academicClassService->create(
            $request->validated()
        );

        return $this->success(
            'Academic Class created successfully.'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicClass $class)
    {
        // dd($classes);
        return $this->success(
            'Academic Classes fetched successfully.',
            $class
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AcademicClassRequest $request, AcademicClass $class)
    {
        $this->academicClassService->update(
            $class->id,
            $request->validated()
        );

        return $this->success(
            'Academic Class Updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicClass $class)
    {
        $this->academicClassService->delete(
            $class->id
        );

        return $this->success(
            'Academic Session deleted successfully.'
        );
    }

    /**
     * Change status
     */
    public function changeStatus(AcademicClass $classes)
    {
        $this->academicClassService->changeStatus($classes->id);

        return $this->success(
            'Academic Class status updated successfully.'
        );
    }
}
