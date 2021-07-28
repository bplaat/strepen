<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'amount'
    ];

    // Generate a random image name
    public static function generateImageName($extension)
    {
        if ($extension == 'jpeg') $extension = 'jpg';
        return Str::random(32) . '.' . $extension;
    }

    // A product belongs to many inventories
    public function inventories()
    {
        return $this->belongsToMany(Inventory::class)->withPivot('amount')->withTimestamps();
    }

    // A product belongs to many transactions
    public function transactions()
    {
        return $this->belongsToMany(Transaction::class)->withPivot('amount')->withTimestamps();
    }

    // Search by a query
    public static function search($query)
    {
        return static::where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('description', 'LIKE', '%' . $query . '%');
    }

    // Search collection by a query
    public static function searchCollection($collection, $query)
    {
        return $collection->filter(function ($user) use ($query) {
            return Str::contains(strtolower($user->name), strtolower($query)) ||
                Str::contains(strtolower($user->description), strtolower($query));
        });
    }
}
