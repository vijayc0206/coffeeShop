<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sale;

class SalesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salesData = [
            [
                'quantity' => 1,
                'unit_cost' => 10.00,
                'selling_price' => 23.34,
            ],
            [
                'quantity' => 2,
                'unit_cost' => 20.50,
                'selling_price' => 64.67,
            ],
            [
                'quantity' => 5,
                'unit_cost' => 12.00,
                'selling_price' => 90.00,
            ],
        ];

        foreach ($salesData as $data) {
            Sale::create($data);
        }
    }
}
