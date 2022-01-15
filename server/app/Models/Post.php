<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $hidden = [
        'deleted_at'
    ];

    // Generate a random image name
    public static function generateImageName($extension)
    {
        if ($extension == 'jpeg') {
            $extension = 'jpg';
        }
        $image = Str::random(32) . '.' . $extension;
        if (static::where('image', $image)->count() > 0) {
            return static::generateImageName($extension);
        }
        return $image;
    }

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
}
