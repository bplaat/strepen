<?php

namespace App\Models;

use App\Notifications\LowBalance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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

    protected $hidden = [
        'email_verified_at',
        'password',
        'remember_token',
        'deleted'
    ];

    protected $casts = [
        'birthday' => 'datetime:Y-m-d',
        'email_verified_at' => 'datetime',
        'receive_news' => 'boolean',
        'balance' => 'double',
        'active' => 'boolean',
        'delted' => 'boolean'
    ];

    // Generate a random avatar name
    public static function generateAvatarName($extension)
    {
        if ($extension == 'jpeg') $extension = 'jpg';
        $avatar = Str::random(32) . '.' . $extension;
        if (static::where('avatar', $avatar)->count() > 0) {
            return static::generateAvatarName($extension);
        }
        return $avatar;
    }

    // Generate a random thanks name
    public static function generateThanksName($extension)
    {
        $thanks = Str::random(32) . '.' . $extension;
        if (static::where('thanks', $thanks)->count() > 0) {
            return static::generateThanksName($extension);
        }
        return $thanks;
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

    // A user has many inventories
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    // A user has many transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Turn model to api data
    public function forApi($user)
    {
        if ($this->avatar != null) {
            $this->avatar = asset('/storage/avatars/' . $this->avatar);
        }
        if ($this->thanks != null) {
            $this->thanks = asset('/storage/thanks/' . $this->thanks);
        }

        if ($user == null || $user->role != User::ROLE_ADMIN) {
            if ($user == null || $this->id != $user->id) {
                unset($this->gender);
                unset($this->birthday);
                unset($this->email);
                unset($this->phone);
                unset($this->address);
                unset($this->postcode);
                unset($this->city);
                unset($this->role);
                unset($this->language);
                unset($this->theme);
                unset($this->receive_news);
                unset($this->balance);
                unset($this->active);
                unset($this->created_at);
            }
            unset($this->updated_at);
        } else {
            if ($this->gender == static::GENDER_MALE) $this->gender = 'male';
            if ($this->gender == static::GENDER_FEMALE) $this->gender = 'female';
            if ($this->gender == static::GENDER_OTHER) $this->gender = 'other';

            if ($this->role == static::ROLE_NORMAL) $this->role = 'normal';
            if ($this->role == static::ROLE_ADMIN) $this->role = 'admin';

            if ($this->language == static::LANGUAGE_ENGLISH) $this->language = 'en';
            if ($this->language == static::LANGUAGE_DUTCH) $this->language = 'nl';

            if ($this->theme == static::THEME_LIGHT) $this->theme = 'light';
            if ($this->theme == static::THEME_DARK) $this->theme = 'dark';
        }
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
    public function getBalanceChart()
    {
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

    // Send all users that have a to low balance an low balance notification
    public static function checkBalances()
    {
        $users = User::where('active', true)->where('deleted', false)->get();
        foreach ($users as $user) {
            if ($user->balance < config('balance.min')) {
                $user->notify(new LowBalance($user));
            }
        }
    }
}
