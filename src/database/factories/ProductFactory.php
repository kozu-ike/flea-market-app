<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
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
        $user = User::inRandomOrder()->first();
        $userId = $user ? $user->id : null;

        return [
            'user_id' => $user ? $user->id : User::factory(),
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'brand' => $this->faker->word,
            'image' => 'default.png',
            'condition' => $this->faker->randomElement(['新品・未使用', '未使用に近い', '目立った傷や汚れなし', 'やや傷や汚れあり', '傷や汚れあり', '全体的に状態が悪い']),
            'stock' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->numberBetween(1000, 100000),
            'status' => 'available',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withCategories()
    {
        return $this->afterCreating(function (Product $product) {
            $categories = Category::inRandomOrder()->limit(2)->pluck('id')->toArray();
            $product->categories()->attach($categories);
        });
    }
}
