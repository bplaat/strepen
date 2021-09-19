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
    public static function search($query, $searchQuery)
    {
        return $query->where('deleted', false)
            ->where(function ($query) use ($searchQuery) {
                $query->where('title', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('body', 'LIKE', '%' . $searchQuery . '%');
            });
    }
}
