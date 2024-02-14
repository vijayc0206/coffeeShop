<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Sale;

class SaleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_sales_page()
    {
        $response = $this->get('/sales');

        $response->assertStatus(200);
        $response->assertViewIs('sales');
    }

    public function test_calculate_selling_price()
    {
        $data = [
            'quantity' => 2,
            'unitCost' => 15.00,
        ];

        $response = $this->post('/calculate-selling-price', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'selling_price',
            'previous_sales' => [],
        ]);
    }

    public function test_calculate_and_store_sale()
    {
        $data = [
            'quantity' => 3,
            'unitCost' => 20.00,
        ];

        $response = $this->post('/calculate-and-store-sale', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'selling_price',
            'previous_sales' => [],
        ]);

        $this->assertDatabaseHas('sales', [
            'quantity' => 3,
            'unit_cost' => 20.00,
        ]);
    }
}
