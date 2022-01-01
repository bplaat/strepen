<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Parsedown;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $hidden = [
        'deleted_at'
    ];

    // A post belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where(fn ($query) => $query->where('title', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('body', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%'));
    }

    // Convert post to API data
    public function toApiData($forUser = null, $includes = [])
    {
        $data = new \stdClass();
        $data->id = $this->id;
        $data->title = $this->title;
        $data->body = (new Parsedown())->text($this->body);
        $data->created_at = $this->created_at;

        if ($forUser != null && ($forUser->role == User::ROLE_MANAGER || $forUser->role == User::ROLE_ADMIN)) {
            $data->updated_at = $this->updated_at;
        }

        if (in_array('user', $includes)) {
            $data->user = $this->user->toApiData($forUser);
        }

        return $data;
    }
}
