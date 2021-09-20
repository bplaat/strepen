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

    // A user can select the english and the dutch language
    const LANGUAGE_ENGLISH = 0;
    const LANGUAGE_DUTCH = 1;

    // A user can select a light and a dark theme
    const THEME_LIGHT = 0;
    const THEME_DARK = 1;

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
        'balance'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean'
    ];

    // Generate a random avatar name
    public static function generateAvatarName($extension)
    {
        if ($extension == 'jpeg') $extension = 'jpg';
        return Str::random(32) . '.' . $extension;
    }

    // Recalculate user balance
    public function recalculateBalance()
    {
        // Refresh relationships
        unset($this->transactions);

        // Recount balance
        $this->balance = 0;

        // Loop through all transactions and adjust balance
        $transactions = $this->transactions()->where('deleted', false)->orderBy('created_at')->get();
        foreach ($transactions as $transaction) {
            if ($transaction->type == Transaction::TYPE_TRANSACTION) {
                $this->balance -= $transaction->price;
            }
            if ($transaction->type == Transaction::TYPE_DEPOSIT) {
                $this->balance += $transaction->price;
            }
            if ($transaction->type == Transaction::TYPE_FOOD) {
                $this->balance -= $transaction->price;
            }
        }
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
    public static function search($query, $searchQuery)
    {
        return $query->where('deleted', false)
            ->where(function ($query) use ($searchQuery) {
                $query->where('firstname', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('insertion', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('lastname', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchQuery . '%');
            });
    }

    // Get balance chart data
    public function getBalanceChart() {
        $balance = 0;
        $balanceData = [];
        $transactions = $this->transactions()->where('deleted', false)->orderBy('created_at')->get();
        foreach ($transactions as $transaction) {
            if ($transaction->type == Transaction::TYPE_TRANSACTION) {
                $balance -= $transaction->price;
            }
            if ($transaction->type == Transaction::TYPE_DEPOSIT) {
                $balance += $transaction->price;
            }
            if ($transaction->type == Transaction::TYPE_FOOD) {
                $balance -= $transaction->price;
            }
            $balanceData[] = [ $transaction->created_at->format('Y-m-d'), $balance ];
        }
        return $balanceData;
    }
}
