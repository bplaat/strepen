<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body'
    ];

    // A post belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Search by a query
    public static function search($query)
    {
        return static::where('title', 'LIKE', '%' . $query . '%')
            ->orWhere('body', 'LIKE', '%' . $query . '%')->get();
    }

    // Search collection by a query
    public static function searchCollection($collection, $query)
    {
        if (strlen($query) == 0) return $collection;
        return $collection->filter(function ($post) use ($query) {
            return Str::contains(strtolower($post->title), strtolower($query)) ||
                Str::contains(strtolower($post->body), strtolower($query));
        });
    }
}
