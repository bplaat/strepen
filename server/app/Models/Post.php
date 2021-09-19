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
    public static function search($searchQuery)
    {
        return static::where('deleted', false)
            ->where(function ($query) use ($searchQuery) {
                $query->where('title', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('body', 'LIKE', '%' . $searchQuery . '%');
            });
    }

    // Search collection by a query
    public static function searchCollection($collection, $searchQuery)
    {
        if (strlen($searchQuery) == 0) {
            return $collection->filter(function ($post) {
                return !$post->deleted;
            });
        }
        return $collection->filter(function ($post) use ($searchQuery) {
            return !$post->deleted && (
                Str::contains(strtolower($post->title), strtolower($searchQuery)) ||
                Str::contains(strtolower($post->body), strtolower($searchQuery))
            );
        });
    }
}
