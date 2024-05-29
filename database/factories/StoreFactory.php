<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'store_name' => $this->faker->company,
            'store_address' => $this->faker->address,
            'store_phone' => $this->faker->phoneNumber,
            'store_email' => $this->faker->companyEmail,
            'user_id' => User::factory(),
        ];
    }
}
