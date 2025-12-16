<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        if ($setting->type === 'boolean') {
            return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
        }
        if ($setting->type === 'json' || $setting->type === 'array') {
            return json_decode($setting->value, true);
        }

        return $setting->value;
    }

    /**
     * Set a setting value.
     */
    public static function set($key, $value, $group = 'general', $type = 'string', $description = null)
    {
        $setting = self::firstOrNew(['key' => $key]);
        $setting->value = is_array($value) ? json_encode($value) : $value;
        $setting->group = $group;
        $setting->type = $type;
        if ($description) {
            $setting->description = $description;
        }
        $setting->save();
        return $setting;
    }
}
