<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Models\Testimonial;
use App\Services\Admin\TestimonialService;
use Illuminate\Http\Request;

class TestimonialController extends BaseController
{
    public function __construct(
        protected TestimonialService $testimonialService
    ) {}

    public function index()
    {
        return view('admin.website.testimonials.index');
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'rating' => $request->input('rating'),
            'filter_status' => $request->input('filter_status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $testimonials = $this->testimonialService->getTestimonials(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($testimonials, (int) $request->input('draw', 1));
    }

    public function store(TestimonialRequest $request)
    {
        $this->testimonialService->create(
            $request->validated()
        );

        return $this->success('Testimonial created successfully.');
    }

    public function edit(Testimonial $testimonial)
    {
        return $this->success('Testimonial fetched successfully.', [
            'id' => $testimonial->id,
            'name' => $testimonial->name,
            'role' => $testimonial->role,
            'message' => $testimonial->message,
            'rating' => $testimonial->rating,
            'sort_order' => $testimonial->sort_order,
            'status' => $testimonial->status,
            'image_url' => $testimonial->image_url,
        ]);
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial)
    {
        $this->testimonialService->update(
            $testimonial->id,
            $request->validated()
        );

        return $this->success('Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $this->testimonialService->delete($testimonial->id);

        return $this->success('Testimonial deleted successfully.');
    }

    public function changeStatus(Testimonial $testimonial)
    {
        $this->testimonialService->changeStatus($testimonial->id);

        return $this->success('Testimonial status updated successfully.');
    }
}
