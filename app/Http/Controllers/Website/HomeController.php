<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\BaseController;
use App\Services\Website\HomePageService;
use Illuminate\View\View;

class HomeController extends BaseController
{
    public function __construct(
        protected HomePageService $homePageService
    ) {}

    /**
     * Landing Page
     */
    public function index(): View
    {
        $pageData = $this->homePageService->getPageData();

        return view('website.home', compact('pageData'));
    }

    public function admission(): View
    {
        $pageData = $this->homePageService->getPageData();

        return view('website.admission.index', compact('pageData'));
    }

    public function contact(): View
    {
        return view('website.contact');
    }

    public function news(): View
    {
        $pageData = $this->homePageService->getPageData();

        return view('website.news', compact('pageData'));
    }

    public function events(): View
    {
        return view('website.events');
    }
}
