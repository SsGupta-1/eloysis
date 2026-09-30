<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\Admin\WebsiteSettingService;
use Illuminate\Http\Request;

class WebsiteSettingController extends BaseController
{
    protected WebsiteSettingService $settingService;

    public function __construct(WebsiteSettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function index()
    {
        $settings = $this->settingService->getAllSettings();

        return view('admin.website.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        $this->settingService->updateSettings($data);

        return $this->success('Website settings updated successfully.');
    }
}
