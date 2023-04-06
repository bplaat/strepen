<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstname,
            'price' => $this->faker->numberBetween(25, 500) / 100,
        ];
    }

    public function alcoholic(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'alcoholic' => true,
        ]);
    }
}
