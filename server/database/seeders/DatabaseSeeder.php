<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create system / kiosk account (must by user_id=1!!!)
        $user = new User();
        $user->firstname = config('app.name');
        $user->lastname = 'System';
        $user->email = 'system@' . strtolower(config('app.name'));
        $user->password = Hash::make(Str::random(32));
        $user->balance = 0;
        $user->active = false;
        $user->save();

        // Create admin account
        $user = new User();
        $user->firstname = 'Bastiaan';
        $user->insertion = 'van der';
        $user->lastname = 'Plaat';
        $user->email = 'bastiaan.v.d.plaat@gmail.com';
        $user->password = Hash::make('admin123');
        $user->role = User::ROLE_ADMIN;
        $user->balance = 0;
        $user->save();

        // Create 50 random users
        // User::factory(50)->create();
    }
}
