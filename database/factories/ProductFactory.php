<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'stock_quantity' => $this->faker->numberBetween(1, 100),
            'sku' => $this->faker->unique()->lexify('SKU????'),
            'store_id' =>Store::factory(),
        ];
    }
}
