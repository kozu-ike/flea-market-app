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
        User::create([
            'name' => '赤坂太郎',
            'email' => 'akasaka@example.com',
            'password' => bcrypt('password123'),
            'address' => '福岡県福岡市中央区赤坂1-2-3',
            'postal_code' => '123-4567',
            'building' => '赤坂ビル',
        ]);
    }
}
