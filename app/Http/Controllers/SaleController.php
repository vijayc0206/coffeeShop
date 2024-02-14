<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Validation\ValidationException;


class SaleController extends Controller
{
    
    public function showSales()
    {
        // Get previous sales for display
        $previousSales = Sale::all();

        return view('sales')->with('previousSales', $previousSales);
    }
    
    private function calculateSellingPriceData($quantity, $unitCost)
    {
        $profitMargin = 0.25;
        $shippingCost = 10.00;
        $cost = $quantity * $unitCost;
        $sellingPrice = ($cost / (1 - $profitMargin)) + $shippingCost;
        return $sellingPrice;
    }

    public function calculateSellingPrice(Request $request)
    {
        // Validate the request data
        $request->validate([
            'quantity' => 'required|numeric',
            'unitCost' => 'required|numeric',
        ]);

        // Extract data from the request
        $quantity = $request->input('quantity');
        $unitCost = $request->input('unitCost');

        // Calculate selling price using the common function
        $sellingPrice = $this->calculateSellingPriceData($quantity, $unitCost);

        // Get previous sales for display
        $previousSales = Sale::all();

        // Return the result as JSON
        return response()->json(['selling_price' => $sellingPrice,'previous_sales' => $previousSales]);
    }

    public function calculateAndStoreSale(Request $request)
    {
        // Validate the request data
        $request->validate([
            'quantity' => 'required|numeric',
            'unitCost' => 'required|numeric',
        ]);

        // Extract data from the request
        $quantity = $request->input('quantity');
        $unitCost = $request->input('unitCost');

        // Calculate selling price using the common function
        $sellingPrice = $this->calculateSellingPriceData($quantity, $unitCost);

        // Record the sale in the database
        Sale::create([
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'selling_price' => $sellingPrice,
        ]);

        // Get previous sales for display
        $previousSales = Sale::all();

        // Return the result as JSON
        return response()->json(['selling_price' => $sellingPrice,'previous_sales' => $previousSales]);
    }


}
