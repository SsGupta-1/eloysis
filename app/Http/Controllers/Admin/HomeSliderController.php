<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\HomeSliderRequest;
use App\Models\HomeSlider;
use App\Services\Admin\HomeSliderService;
use Illuminate\Http\Request;

class HomeSliderController extends BaseController
{
    protected HomeSliderService $sliderService;

    public function __construct(HomeSliderService $sliderService)
    {
        $this->sliderService = $sliderService;
    }

    public function index()
    {
        return view('admin.website.home_sliders.index');
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

        $sliders = $this->sliderService->getSliders(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($sliders, (int) $request->input('draw', 1));
    }

    public function store(HomeSliderRequest $request)
    {
        $this->sliderService->create(
            $request->validated()
        );

        return $this->success('Home slider created successfully.');
    }

    public function edit(HomeSlider $home_slider)
    {
        return $this->success('Slider fetched successfully.', [
            'id' => $home_slider->id,
            'title' => $home_slider->title,
            'subtitle' => $home_slider->subtitle,
            'button_text' => $home_slider->button_text,
            'button_url' => $home_slider->button_url,
            'sort_order' => $home_slider->sort_order,
            'status' => $home_slider->status,
            'image_url' => $home_slider->image_url,
        ]);
    }

    public function update(HomeSliderRequest $request, HomeSlider $home_slider)
    {
        $this->sliderService->update(
            $home_slider->id,
            $request->validated()
        );

        return $this->success('Home slider updated successfully.');
    }

    public function destroy(HomeSlider $home_slider)
    {
        $this->sliderService->delete($home_slider->id);

        return $this->success('Home slider deleted successfully.');
    }

    public function changeStatus(HomeSlider $home_slider)
    {
        $this->sliderService->changeStatus($home_slider->id);

        return $this->success('Slider status updated successfully.');
    }
}
