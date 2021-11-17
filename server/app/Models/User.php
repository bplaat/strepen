<?php

namespace App\Models;

use App\Notifications\LowBalance;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
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
        'deleted' => 'boolean'
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

        if ($this->id != 1) {
            // Loop through all transactions and adjust balance
            $transactions = $this->transactions()->where('deleted', false)->get();
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

    // Check if user is minor
    public function getMinorAttribute()
    {
        return $this->birthday != null && $this->birthday->diff(new DateTime('now'))->y < Setting::get('minor_age');
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
            unset($this->updated_at);
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
                return;
            }
        }

        if ($this->gender == static::GENDER_MALE) $this->gender = 'male';
        if ($this->gender == static::GENDER_FEMALE) $this->gender = 'female';
        if ($this->gender == static::GENDER_OTHER) $this->gender = 'other';

        if ($this->role == static::ROLE_NORMAL) $this->role = 'normal';
        if ($this->role == static::ROLE_ADMIN) $this->role = 'admin';

        if ($this->language == static::LANGUAGE_ENGLISH) $this->language = 'en';
        if ($this->language == static::LANGUAGE_DUTCH) $this->language = 'nl';

        if ($this->theme == static::THEME_LIGHT) $this->theme = 'light';
        if ($this->theme == static::THEME_DARK) $this->theme = 'dark';

        $this->minor = $this->getMinorAttribute();
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('deleted', false)
            ->where(function ($query) use ($searchQuery) {
                $query->where('firstname', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('insertion', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('lastname', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
            });
    }

    // Get balance chart data
    public function getBalanceChart($startDate, $endDate)
    {
        // Covert start and end date to timestamp
        $firstTransaction = $this->transactions()->where('deleted', false)->orderBy('created_at')->first();
        if ($firstTransaction != null) {
            $startDate = max(strtotime($startDate), $firstTransaction->created_at->getTimestamp());
        } else {
            $startDate = strtotime(date('Y-m-d'));
        }
        $endDate = min(strtotime($endDate), strtotime(date('Y-m-d')));

        // Get the deposits price sum and transactions price sum before start date
        $startDepositsPrice = DB::table('transactions')
            ->where('user_id', $this->id)
            ->where('deleted', false)
            ->where('type', Transaction::TYPE_DEPOSIT)
            ->where('created_at', '<', date('Y-m-d H:i:s', $startDate))
            ->sum('price');

        $startTransactionsPrice = DB::table('transactions')
            ->where('user_id', $this->id)
            ->where('deleted', false)
            ->where(function ($query) {
                $query->where('type', Transaction::TYPE_TRANSACTION)
                    ->orWhere('type', Transaction::TYPE_FOOD);
            })
            ->where('created_at', '<', date('Y-m-d H:i:s', $startDate))
            ->sum('price');

        // Get the rest of the transactions between this time
        $transactions = $this->transactions()->where('deleted', false)->orderBy('created_at')
            ->where('created_at', '>=', date('Y-m-d H:i:s', $startDate))
            ->where('created_at', '<', date('Y-m-d H:i:s', $endDate + 24 * 60 * 60))
            ->get();

        // Loop trough days
        $balance = $startDepositsPrice - $startTransactionsPrice;
        $days = ($endDate - $startDate + 1) / (24 * 60 * 60);
        $balanceData = [];
        $index = 0;
        for ($day = 0; $day < $days; $day++) {
            $dayTime = $startDate + $day * (24 * 60 * 60);
            // Ajust balance by using the transactions of that day
            while (
                $index < $transactions->count() &&
                $transactions[$index]->created_at->getTimestamp() >= $dayTime &&
                $transactions[$index]->created_at->getTimestamp() < $dayTime + (24 * 60 * 60)
            ) {
                if ($transactions[$index]->type == Transaction::TYPE_TRANSACTION) {
                    $balance -= $transactions[$index]->price;
                }
                if ($transactions[$index]->type == Transaction::TYPE_DEPOSIT) {
                    $balance += $transactions[$index]->price;
                }
                if ($transactions[$index]->type == Transaction::TYPE_FOOD) {
                    $balance -= $transactions[$index]->price;
                }
                $index++;
            }
            $balanceData[] = [ date('Y-m-d', $dayTime), $balance ];
        }

        return $balanceData;
    }

    // Send all users that have a to low balance an low balance notification
    public static function checkBalances()
    {
        $users = User::where('active', true)->where('deleted', false)->get();
        $minUserBalance = Setting::get('min_user_balance');
        foreach ($users as $user) {
            if ($user->balance < $minUserBalance) {
                $user->notify(new LowBalance($user));
            }
        }
    }
}
