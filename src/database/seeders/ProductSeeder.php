<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
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

        $shoulderBag = Product::create([
            'name' => 'ショルダーバッグ',
            'price' => 3500,
            'description' => 'おしゃれなショルダーバッグ',
            'brand' => null,
            'image' => 'bag.png',
            'condition' => 'やや傷や汚れあり',
            'stock' => 1,
            'user_id' => $userId,
        ]);
        $shoulderBag->categories()->attach($categories['ファッション']->id);

        $watch = Product::create([
            'name' => '腕時計',
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'brand' => 'メンズ腕時計ブランド',
            'image' => 'clock.png',
            'condition' => '良好',
            'stock' => 1,
            'user_id' => $userId,
        ]);
        $watch->categories()->attach($categories['ファッション']->id);

        $hardDrive = Product::create([
            'name' => 'HDD',
            'price' => 5000,
            'description' => '高速で信頼性の高いハードディスク',
            'brand' => null,
            'image' => 'harddisk.png',
            'condition' => '目立った傷や汚れなし',
            'stock' => 1,
            'user_id' => $userId,
        ]);
        $hardDrive->categories()->attach($categories['家電']->id);

        $onionBundle = Product::create([
            'name' => '玉ねぎ3束',
            'price' => 300,
            'description' => '新鮮な玉ねぎ3束のセット',
            'brand' => null,
            'image' => 'onion.png',
            'condition' => 'やや傷や汚れあり',
            'stock' => 1,
            'user_id' => $userId,
        ]);
        $onionBundle->categories()->attach($categories['キッチン']->id);

        $leatherShoes = Product::create([
            'name' => '革靴',
            'price' => 4000,
            'description' => 'クラシックなデザインの革靴',
            'brand' => 'クロケット＆ジョーンズ',
            'image' => 'shoes.png',
            'condition' => '状態が悪い',
            'stock' => 1,
            'user_id' => $userId,
        ]);
        $leatherShoes->categories()->attach($categories['ファッション']->id);

        $laptop = Product::create([
            'name' => 'ノートPC',
            'price' => 45000,
            'description' => '高性能なノートパソコン',
            'brand' => 'アップル',
            'image' => 'pc.png',
            'condition' => '良好',
            'stock' => 1,
            'user_id' => $userId,
        ]);
        $laptop->categories()->attach($categories['家電']->id);

        $microphone = Product::create([
            'name' => 'マイク',
            'price' => 8000,
            'description' => '高音質のレコーディング用マイク',
            'brand' => 'ソニー',
            'image' => 'microphone.png',
            'condition' => '目立った傷や汚れなし',
            'stock' => 1,
            'user_id' => $userId,
        ]);
        $microphone->categories()->attach($categories['家電']->id);

        $tumbler = Product::create([
            'name' => 'タンブラー',
            'price' => 500,
            'description' => '使いやすいタンブラー',
            'brand' => 'STANLEY',
            'image' => 'tumbler.png',
            'condition' => '状態が悪い',
            'stock' => 1,
            'user_id' => $userId,
        ]);
        $tumbler->categories()->attach($categories['キッチン']->id);

        $coffeeMill = Product::create([
            'name' => 'コーヒーミル',
            'price' => 4000,
            'description' => '手動のコーヒーミル',
            'brand' => 'カリタ',
            'image' => 'mill.png',
            'condition' => '良好',
            'stock' => 1,
            'user_id' => $userId,
        ]);
        $coffeeMill->categories()->attach($categories['キッチン']->id);

        $makeupSet = Product::create([
            'name' => 'メイクセット',
            'price' => 2500,
            'description' => '便利なメイクアップセット',
            'brand' => null,
            'image' => 'makeup.png',
            'condition' => '目立った傷や汚れなし',
            'stock' => 1,
            'user_id' => $userId,
        ]);
        $makeupSet->categories()->attach($categories['ファッション']->id);
    }
}
