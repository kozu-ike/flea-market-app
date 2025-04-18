<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;


class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $methods = [
            ['name' => 'カード支払い'],
            ['name' => 'コンビニ支払い'],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
}
