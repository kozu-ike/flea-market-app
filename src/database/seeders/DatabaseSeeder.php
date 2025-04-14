<?php

namespace Database\Seeders;

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
        // ① ユーザー
        $this->call(UserSeeder::class);

        // ② カテゴリー
        $this->call(CategorySeeder::class);

        // ③ 商品（ユーザーとカテゴリに依存してる）
        $this->call(ProductSeeder::class);
    }
}
