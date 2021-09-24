<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $hidden = [
        'deleted'
    ];

    protected $casts = [
        'deleted' => 'boolean'
    ];

    // A post belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Turn model to api data
    public function forApi($user, $parsedown)
    {
        $this->body = $parsedown->text($this->body);

        $this->user->forApi(null); // TEMP = $user

        if ($user == null || $user->role != User::ROLE_ADMIN) {
            unset($this->updated_at);
        }
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
