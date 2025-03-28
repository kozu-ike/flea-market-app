<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Condition;
use App\Models\Category;
use App\Models\User;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = User::first()->id;

        $categories = Category::whereIn('name', ['ファッション', '家電', 'キッチン'])->get()->keyBy('name');

        $conditions = Condition::whereIn('name', ['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'])->get()->keyBy('name');

        Product::create([
            'name' => 'ショルダーバッグ',
            'price' => 3500,
            'description' => 'おしゃれなショルダーバッグ',
            'brand' => null,
            'image' => 'bag.png',
            'category_id' => $categories['ファッション']->id,
            'condition_id' => $conditions['やや傷や汚れあり']->id,
            'stock' => rand(1, 2),
            'user_id' => $userId,
        ]);

        Product::create([
            'name' => '腕時計',
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'brand' => 'メンズ腕時計ブランド',
            'image' => 'clock.png',
            'category_id' => $categories['ファッション']->id,
            'condition_id' => $conditions['良好']->id,
            'stock' => rand(1, 2),
            'user_id' => $userId,
        ]);

        Product::create([
            'name' => 'HDD',
            'price' => 5000,
            'description' => '高速で信頼性の高いハードディスク',
            'brand' => null,
            'image' => 'harddisk.png',
            'category_id' => $categories['家電']->id,
            'condition_id' => $conditions['目立った傷や汚れなし']->id,
            'stock' => rand(1, 2),
            'user_id' => $userId,
        ]);

        Product::create([
            'name' => '玉ねぎ3束',
            'price' => 300,
            'description' => '新鮮な玉ねぎ3束のセット',
            'brand' => null,
            'image' => 'onion.png',
            'category_id' => $categories['キッチン']->id,
            'condition_id' => $conditions['やや傷や汚れあり']->id,
            'stock' => rand(1, 2),
            'user_id' => $userId,
        ]);

        Product::create([
            'name' => '革靴',
            'price' => 4000,
            'description' => 'クラシックなデザインの革靴',
            'brand' => 'クロケット＆ジョーンズ',
            'image' => 'shoes.png',
            'category_id' => $categories['ファッション']->id,
            'condition_id' => $conditions['状態が悪い']->id,
            'stock' => rand(1, 2),
            'user_id' => $userId,
        ]);

        Product::create([
            'name' => 'ノートPC',
            'price' => 45000,
            'description' => '高性能なノートパソコン',
            'brand' => 'アップル',
            'image' => 'pc.png',
            'category_id' => $categories['家電']->id,
            'condition_id' => $conditions['良好']->id,
            'stock' => rand(1, 2),
            'user_id' => $userId,
        ]);

        Product::create([
            'name' => 'マイク',
            'price' => 8000,
            'description' => '高音質のレコーディング用マイク',
            'brand' => 'ソニー',
            'image' => 'microphone.png',
            'category_id' => $categories['家電']->id,
            'condition_id' => $conditions['目立った傷や汚れなし']->id,
            'stock' => rand(1, 2),
            'user_id' => $userId,
        ]);

        Product::create([
            'name' => 'タンブラー',
            'price' => 500,
            'description' => '使いやすいタンブラー',
            'brand' => 'STANLEY',
            'image' => 'tumbler.png',
            'category_id' => $categories['キッチン']->id,
            'condition_id' => $conditions['状態が悪い']->id,
            'stock' => rand(1, 2),
            'user_id' => $userId,
        ]);

        Product::create([
            'name' => 'コーヒーミル',
            'price' => 4000,
            'description' => '手動のコーヒーミル',
            'brand' => 'カリタ',
            'image' => 'mill.png',
            'category_id' => $categories['キッチン']->id,
            'condition_id' => $conditions['良好']->id,
            'stock' => rand(1, 2),
            'user_id' => $userId,
        ]);

        Product::create([
            'name' => 'メイクセット',
            'price' => 2500,
            'description' => '便利なメイクアップセット',
            'brand' => null,
            'image' => 'makeup.png',
            'category_id' => $categories['ファッション']->id,
            'condition_id' => $conditions['目立った傷や汚れなし']->id,
            'stock' => rand(1, 2),
            'user_id' => $userId,
        ]);
    }
}
