<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get setting value by key with optional default
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting key-value pair
     */
    public static function set(string $key, mixed $value, string $group = 'general'): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    /**
     * Get all settings as key => value array for a group or all
     */
    public static function getGroup(?string $group = null): array
    {
        $query = static::query();

        if ($group) {
            $query->where('group', $group);
        }

        return $query->pluck('value', 'key')->toArray();
    }
}
