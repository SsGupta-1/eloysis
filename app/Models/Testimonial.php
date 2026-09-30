<?php

namespace App\Models;

use App\Helpers\UploadHelper;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'role',
        'image',
        'message',
        'rating',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return asset('assets/website/images/slider/slider-1.png');
        }

        if (str_starts_with($this->image, 'http') || str_starts_with($this->image, 'storage/')) {
            return asset($this->image);
        }

        return UploadHelper::url($this->image);
    }
}
