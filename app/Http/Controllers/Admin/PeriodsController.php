<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\PeriodRequest;
use App\Models\Periods;
use App\Services\Admin\PeriodService;
use Illuminate\Http\Request;

class PeriodsController extends BaseController
{
    protected $periodService;

    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }

    // index
    public function index()
    {
        return view('admin.periods.index');
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

        $periods = $this->periodService->getLists(
            $filters,
            $page,
            $length,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($periods, (int) $request->input('draw', 1));
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
    public function store(PeriodRequest $request)
    {
        // dd($request->all());
        $this->periodService->create(
            $request->validated()
        );

        return $this->success(
            'Period created successfully.'
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
    public function edit(Periods $period)
    {
        // dd($classes);
        return $this->success(
            'Period fetched successfully.',
            $period
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PeriodRequest $request, Periods $period)
    {
        $this->periodService->update(
            $period->id,
            $request->validated()
        );

        return $this->success(
            'Period updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Periods $period)
    {
        $this->periodService->delete(
            $period->id
        );

        return $this->success(
            'Period deleted successfully.'
        );
    }

    /**
     * Change status
     */
    public function changeStatus(Periods $periods)
    {
        $this->periodService->changeStatus($periods->id);

        return $this->success(
            'Period status updated successfully.'
        );
    }
}
