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
        // Create kiosk account (must by user_id=1)
        User::create([
            'firstname' => 'Strepen',
            'lastname' => 'Kiosk',
            'email' => 'kiosk@strepen',
            'password' => Hash::make(Str::random(32)),
            'role' => User::ROLE_NORMAL,
            'balance' => 0
        ]);

        // Create admin account
        User::create([
            'firstname' => 'Bastiaan',
            'insertion' => 'van der',
            'lastname' => 'Plaat',
            'email' => 'bastiaan.v.d.plaat@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => User::ROLE_ADMIN,
            'balance' => 0
        ]);

        // Create 50 random users
        User::factory(50)->create();
    }
}
