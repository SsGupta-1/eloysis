<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ClassSectionRequest;
use App\Models\ClassSection;
use App\Services\Admin\ClassSectionService;
use Illuminate\Http\Request;

class ClassSectionController extends BaseController
{
    public function __construct(protected ClassSectionService $classSectionService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view(
            'admin.class_sections.index',
            [
                'classes' => class_options(),
                'sections' => section_options(),
            ]
        );
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

        $sections = $this->classSectionService->getLists(
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
        $data = [
            'class' => class_options(),
            'section' => section_options(),
        ];

        // dd($class);
        return $this->success(
            'Class Section Create modal open successfully',
            $data ?? []
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClassSectionRequest $request)
    {
        $sections = $this->classSectionService->create(
            $request->validated()
        );

        return $this->success(
            'Academic Class Sections create successfully.',
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
    public function edit(ClassSection $class_section)
    {
        $class_section['class_info'] = class_options();
        $class_section['section_info'] = section_options();

        return $this->success(
            'Academic Class Sections fetch successfully.',
            $class_section
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClassSectionRequest $request, ClassSection $class_section)
    {
        $sections = $this->classSectionService->update(
            $class_section->id,
            $request->validated()
        );

        return $this->success(
            'Academic Class Sections update successfully.',
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSection $class_section)
    {
        $sections = $this->classSectionService->delete(
            $class_section->id
        );

        return $this->success(
            'Academic Class Sections Deleted successfully.',
        );
    }

    /**
     * Change status
     */
    public function changeStatus(ClassSection $classSection)
    {
        $this->classSectionService->changeStatus($classSection->id);

        return $this->success(
            'Academic Class Section status updated successfully.'
        );
    }
}
