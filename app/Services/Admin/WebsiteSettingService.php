<?php

namespace App\Services\Admin;

use App\Helpers\UploadHelper;
use App\Models\WebsiteSetting;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class WebsiteSettingService
{
    public function getAllSettings(): array
    {
        return WebsiteSetting::pluck('value', 'key')->toArray();
    }

    public function updateSettings(array $data)
    {
        DB::beginTransaction();

        try {
            // Handle file uploads (e.g. logo, principal_image, principal_signature, about_image)
            $imageKeys = ['institute_logo', 'principal_image', 'principal_signature', 'about_image'];

            foreach ($imageKeys as $imageKey) {
                if (isset($data[$imageKey]) && $data[$imageKey] instanceof UploadedFile) {
                    $oldImage = WebsiteSetting::get($imageKey);
                    $data[$imageKey] = UploadHelper::replace($data[$imageKey], $oldImage, 'assets/uploads/website/settings');
                }
            }

            foreach ($data as $key => $value) {
                if ($value !== null && ! ($value instanceof UploadedFile)) {
                    WebsiteSetting::set($key, (string) $value);
                } elseif ($value !== null && is_string($value)) {
                    WebsiteSetting::set($key, $value);
                }
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
