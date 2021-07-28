<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create admin account
        User::create([
            'firstname' => 'Bastiaan',
            'insertion' => 'van der',
            'lastname' => 'Plaat',
            'email' => 'bastiaan.v.d.plaat@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => User::ROLE_ADMIN,
            'money' => 0
        ]);

        // Create 50 random users
        User::factory(50)->create();
    }
}
