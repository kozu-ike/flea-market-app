<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        $user = User::first();
        $product = Product::first();

        return [
            'user_id' => $user ? $user->id : User::factory(),
            'product_id' => $product ? $product->id : Product::factory(),
            'content' => $this->faker->sentence,
        ];
    }
}
