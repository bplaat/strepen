<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public static function get($key) {
        return static::where('key', $key)->first()->value;
    }

    public static function set($key, $value) {
        $setting = static::where('key', $key)->first();
        $setting->value = $value;
        $setting->save();
    }
}
