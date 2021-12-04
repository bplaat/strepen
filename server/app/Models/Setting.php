<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    private static $cache = [];

    public static function get($key) {
        if (!isset(static::$cache[$key])) {
            static::$cache[$key] = static::where('key', $key)->first()->value;
        }
        return static::$cache[$key];
    }

    public static function set($key, $value) {
        $setting = static::where('key', $key)->first();
        $setting->value = $value;
        static::$cache[$key] = $value;
        $setting->save();
    }
}
