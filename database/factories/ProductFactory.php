<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'category' => $this->faker->randomElement(['Elektronik', 'Pakaian', 'Rumah Tangga', 'Olahraga']),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(10000, 5000000),
            'stock' => $this->faker->numberBetween(0, 100),
            'image' => null,
            'is_active' => true,
        ];
    }
}
