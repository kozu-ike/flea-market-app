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
        // ランダムにユーザーを取得
        $user = User::inRandomOrder()->first();  // ユーザーをランダムに取得
        $userId = $user ? $user->id : null;

        // 商品データを生成
        return [
            'user_id' => $user ? $user->id : User::factory(), // こう書くのが安全
            'name' => $this->faker->word,  // 商品名を生成
            'description' => $this->faker->sentence,  // 商品説明を生成
            'brand' => $this->faker->word,  // ブランドを生成
            'image' => 'default.png',  // デフォルトの画像名（ファイル名だけ、実際のファイルはストレージに保存する）
            'condition' => $this->faker->randomElement(['新品・未使用', '未使用に近い', '目立った傷や汚れなし', 'やや傷や汚れあり', '傷や汚れあり', '全体的に状態が悪い']),  // ランダムに状態を選択
            'stock' => $this->faker->numberBetween(1, 10),  // ランダムに在庫数を生成
            'price' => $this->faker->numberBetween(1000, 100000),  // ランダムに価格を生成
            'status' => 'available',  // デフォルトで「available」と設定
        ];
    }

    /**
     * 商品にランダムなカテゴリを割り当てる
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withCategories()
    {
        return $this->afterCreating(function (Product $product) {
            // ランダムに2つのカテゴリIDを取得
            $categories = Category::inRandomOrder()->limit(2)->pluck('id')->toArray();  // 配列に変換

            // 商品にカテゴリを関連付け（categories() は belongsToMany リレーション）
            $product->categories()->attach($categories);
        });
    }
}
