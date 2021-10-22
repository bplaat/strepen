<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $hidden = [
        'deleted'
    ];

    protected $casts = [
        'active' => 'boolean',
        'deleted' => 'boolean'
    ];

    public static function generateKey()
    {
        $key = Str::random(32);
        if (static::where('key', $key)->count() > 0) {
            return static::generateKey();
        }
        return $key;
    }

    public static function search($query, $searchQuery)
    {
        return $query->where('deleted', false)
            ->where(function ($query) use ($searchQuery) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
            });
    }
}
