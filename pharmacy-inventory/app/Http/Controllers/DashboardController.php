<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Get all products with their categories
        $products = Product::with('category')
            ->orderBy('name')
            ->get();

        // Get statistics
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('quantity', '<', 10)->count();
        $totalValue = Product::sum(DB::raw('quantity * price'));
        $totalCategories = Category::count();

        // Totals for purchases and sales and available stock
        $totalSalesAmount = Sale::sum('total_price');
        $totalPurchasesCost = Purchase::sum('total_cost');
        $totalAvailableStock = Product::sum('quantity');

        // Prepare stats for the view
        $stats = [
            'total_products' => $totalProducts,
            'low_stock_products' => $lowStockProducts,
            'total_value' => number_format($totalValue, 2),
            'total_categories' => $totalCategories,
            'total_sales_amount' => number_format($totalSalesAmount, 2),
            'total_purchases_cost' => number_format($totalPurchasesCost, 2),
            'total_available_stock' => $totalAvailableStock,
        ];

        // Categories for create/update forms
        $categories = Category::orderBy('name')->get();

        // Data for Chart.js
        $chartData = [
            'labels' => ['Purchases', 'Sales', 'Stock'],
            'values' => [
                round($totalPurchasesCost, 2),
                round($totalSalesAmount, 2),
                (int) $totalAvailableStock,
            ],
        ];

        return view('dashboard', compact('products', 'stats', 'categories', 'chartData'));
    }


}

