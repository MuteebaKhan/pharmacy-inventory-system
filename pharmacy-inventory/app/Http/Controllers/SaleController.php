<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Store a newly created sale.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity_sold' => 'required|integer|min:1',
                'sale_date' => 'nullable|date',
            ]);

            // Start database transaction
            DB::beginTransaction();

            try {
                // Get the product
                $product = Product::findOrFail($validated['product_id']);

                // Check if product has enough stock
                if ($product->quantity < $validated['quantity_sold']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock. Available: ' . $product->quantity,
                    ], 422);
                }

                // Calculate total price
                $totalPrice = $validated['quantity_sold'] * $product->price;

                // Set sale date if not provided
                $saleDate = $validated['sale_date'] ?? now()->toDateString();

                // Create the sale
                $sale = Sale::create([
                    'product_id' => $validated['product_id'],
                    'quantity_sold' => $validated['quantity_sold'],
                    'total_price' => $totalPrice,
                    'sale_date' => $saleDate,
                ]);

                // Update product quantity
                $product->quantity -= $validated['quantity_sold'];
                $product->save();

                // Commit transaction
                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => $sale->load('product'),
                    'message' => 'Sale completed successfully'
                ], 201);

            } catch (\Exception $e) {
                // Rollback transaction on error
                DB::rollBack();
                throw $e;
            }

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of sales.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Sale::with('product.category');

            // Filter by product if provided
            if ($request->has('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            // Filter by date range if provided
            if ($request->has('start_date')) {
                $query->where('sale_date', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->where('sale_date', '<=', $request->end_date);
            }

            $sales = $query->orderBy('sale_date', 'desc')->get();

            // Calculate totals
            $totalSales = $sales->sum('total_price');
            $totalQuantity = $sales->sum('quantity_sold');

            return response()->json([
                'success' => true,
                'data' => $sales,
                'summary' => [
                    'total_sales' => number_format($totalSales, 2),
                    'total_quantity_sold' => $totalQuantity,
                    'count' => $sales->count(),
                ],
                'message' => 'Sales retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sales',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

