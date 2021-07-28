<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
            ->orWhere('body', 'LIKE', '%' . $query . '%');
    }

    // Search collection by a query
    public static function searchCollection($collection, $query)
    {
        return $collection->filter(function ($user) use ($query) {
            return Str::contains(strtolower($user->title), strtolower($query)) ||
                Str::contains(strtolower($user->body), strtolower($query));
        });
    }
}
