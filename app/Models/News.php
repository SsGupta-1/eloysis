<?php

namespace App\Models;

use App\Helpers\UploadHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'title',
        'slug',
        'image',
        'published_date',
        'summary',
        'content',
        'url',
        'status',
    ];

    protected $casts = [
        'published_date' => 'date',
        'status' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->title).'-'.Str::random(5);
            }
        });
    }

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
