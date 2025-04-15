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
        // ユーザーと商品は Seeder で作成されていることを前提とする
        // Seeder で作成されたデータを取得
        $user = User::first();  // 事前に Seeder で作成されたユーザーを取得
        $product = Product::first();  // 事前に Seeder で作成された商品を取得

        // Seeder から取得できなかった場合は、Factory で生成
        return [
            'user_id' => $user ? $user->id : User::factory(),
            'product_id' => $product ? $product->id : Product::factory(),
            'content' => $this->faker->sentence,  // ランダムな文を生成
        ];
    }
}
