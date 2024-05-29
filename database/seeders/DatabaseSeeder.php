<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       User::factory(10)->create()->each(function ($user){
        Store::factory(rand(1, 3))->create(['user_id' => $user->id])->each(function ($store){
            Product::factory(rand(1, 4))->create(['store_id' => $store->id]);
        });
       });
    }
}
