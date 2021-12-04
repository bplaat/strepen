<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'firstname' => $this->faker->firstName,
            'insertion' => $this->faker->randomElement([null, null, null, 'van', 'de', 'van der']),
            'lastname' => $this->faker->lastName,
            'gender' => $this->faker->randomElement([User::GENDER_MALE, User::GENDER_FEMALE, User::GENDER_OTHER]),
            'birthday' => $this->faker->date(),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->streetAddress,
            'postcode' => $this->faker->postcode,
            'city' => $this->faker->city,
            'password' => Hash::make($this->faker->password),
            'balance' => 0
        ];
    }

    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function password($password)
    {
        return $this->state(function (array $attributes) use ($password) {
            return [
                'password' => Hash::make($password)
            ];
        });
    }
}
