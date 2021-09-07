<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // A user can be male, female or other
    const GENDER_MALE = 0;
    const GENDER_FEMALE = 1;
    const GENDER_OTHER = 2;

    // A user can be normal or an admin
    const ROLE_NORMAL = 0;
    const ROLE_ADMIN = 1;

    protected $fillable = [
        'firstname',
        'insertion',
        'lastname',
        'avatar',
        'gender',
        'birthday',
        'email',
        'phone',
        'address',
        'postcode',
        'city',
        'password',
        'role',
        'money'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        // 'birthday' => 'datetime',
        'email_verified_at' => 'datetime'
    ];

    // Generate a random avatar name
    public static function generateAvatarName($extension)
    {
        if ($extension == 'jpeg') $extension = 'jpg';
        return Str::random(32) . '.' . $extension;
    }

    // Get user full name (firstname insertion lastname)
    public function getNameAttribute()
    {
        if ($this->insertion != null) {
            return $this->firstname . ' ' . $this->insertion . ' ' . $this->lastname;
        } else {
            return $this->firstname . ' ' . $this->lastname;
        }
    }

    // Get user sort name (lastname, insertion firstname)
    public function getSortNameAttribute()
    {
        if ($this->insertion != null) {
            return $this->lastname . ', ' . $this->insertion . ' ' . $this->firstname;
        } else {
            return $this->lastname . ' ' . $this->firstname;
        }
    }

    // A user has many posts
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // A user has many transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Search by a query
    public static function search($query)
    {
        return static::where('firstname', 'LIKE', '%' . $query . '%')
            ->orWhere('insertion', 'LIKE', '%' . $query . '%')
            ->orWhere('lastname', 'LIKE', '%' . $query . '%')
            ->orWhere('email', 'LIKE', '%' . $query . '%')->get();
    }

    // Search collection by a query
    public static function searchCollection($collection, $query)
    {
        return $collection->filter(function ($user) use ($query) {
            return Str::contains(strtolower($user->firstname), strtolower($query)) ||
                Str::contains(strtolower($user->insertion), strtolower($query)) ||
                Str::contains(strtolower($user->lastname), strtolower($query)) ||
                Str::contains(strtolower($user->email), strtolower($query));
        });
    }
}
