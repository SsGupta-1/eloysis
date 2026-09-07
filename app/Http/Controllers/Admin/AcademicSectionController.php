<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AcademicSectionRequest;
use App\Models\Section;
use App\Services\Admin\AcademicSectionService;
use Illuminate\Http\Request;

class AcademicSectionController extends BaseController
{
    protected AcademicSectionService $AcademicSectionService;

    public function __construct(AcademicSectionService $AcademicSectionService)
    {
        $this->AcademicSectionService = $AcademicSectionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.sections.index');
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

        $sections = $this->AcademicSectionService->getAcademicSections(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($sections, (int) $request->input('draw', 1));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $class = class_options();

        // dd($class);
        return $this->success(
            'Section Create modal open successfully',
            $class ?? []
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicSectionRequest $request)
    {
        $sections = $this->AcademicSectionService->create(
            $request->validated()
        );

        return $this->success(
            'Academic Sections create successfully.',
            $sections
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
    public function edit(Section $section)
    {
        $section['class_info'] = class_options();

        // dd($section);
        return $this->success(
            'Academic Sections fetch successfully.',
            $section
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AcademicSectionRequest $request, Section $section)
    {
        $sections = $this->AcademicSectionService->update(
            $section->id,
            $request->validated()
        );

        return $this->success(
            'Academic Sections update successfully.',
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        $sections = $this->AcademicSectionService->delete(
            $section->id
        );

        return $this->success(
            'Academic Sections Deleted successfully.',
        );
    }

    /**
     * Change status
     */
    public function changeStatus(Section $sections)
    {
        $this->AcademicSectionService->changeStatus($sections->id);

        return $this->success(
            'Academic Section status updated successfully.'
        );
    }
}
