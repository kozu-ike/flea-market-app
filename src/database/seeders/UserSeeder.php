<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'id' => 1,
                'name' => '赤坂太郎',
                'email' => 'akasaka@example.com',
                'password' => bcrypt('password'),
                'address' => '福岡県福岡市中央区赤坂1-2-3',
                'postal_code' => '123-4567',
                'building' => '赤坂ビル',
            ],
            [
                'id' => 2,
                'name' => '山田花子',
                'email' => 'yamada@example.com',
                'password' => bcrypt('password456'),
                'address' => '東京都渋谷区渋谷2-1-1',
                'postal_code' => '234-5678',
                'building' => '渋谷ビル',
            ],
            [
                'id' => 3,
                'name' => '佐藤一郎',
                'email' => 'sato@example.com',
                'password' => bcrypt('sato123'),
                'address' => '大阪府大阪市北区梅田1-1-1',
                'postal_code' => '345-6789',
                'building' => '梅田タワー',
            ],
        ];

        foreach ($users as $userData) {
            $existingUser = User::where('email', $userData['email'])->first();

            if (!$existingUser) {
                User::create($userData);
            }
        }
    }
}
