<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Store a newly created purchase.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity_purchased' => 'required|integer|min:1',
                'unit_cost' => 'required|numeric|min:0',
                'purchase_date' => 'nullable|date',
            ]);

            // Start database transaction
            DB::beginTransaction();

            try {
                // Get the product
                $product = Product::findOrFail($validated['product_id']);

                // Calculate total cost
                $totalCost = $validated['quantity_purchased'] * $validated['unit_cost'];

                // Set purchase date if not provided
                $purchaseDate = $validated['purchase_date'] ?? now()->toDateString();

                // Create the purchase
                $purchase = Purchase::create([
                    'product_id' => $validated['product_id'],
                    'quantity_purchased' => $validated['quantity_purchased'],
                    'total_cost' => $totalCost,
                    'purchase_date' => $purchaseDate,
                ]);

                // Update product quantity
                $product->quantity += $validated['quantity_purchased'];
                $product->save();

                // Commit transaction
                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => $purchase->load('product'),
                    'message' => 'Purchase completed successfully'
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
                'message' => 'Failed to create purchase',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of purchases.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Purchase::with('product.category');

            // Filter by product if provided
            if ($request->has('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            // Filter by date range if provided
            if ($request->has('start_date')) {
                $query->where('purchase_date', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->where('purchase_date', '<=', $request->end_date);
            }

            $purchases = $query->orderBy('purchase_date', 'desc')->get();

            // Calculate totals
            $totalCost = $purchases->sum('total_cost');
            $totalQuantity = $purchases->sum('quantity_purchased');

            return response()->json([
                'success' => true,
                'data' => $purchases,
                'summary' => [
                    'total_cost' => number_format($totalCost, 2),
                    'total_quantity_purchased' => $totalQuantity,
                    'count' => $purchases->count(),
                ],
                'message' => 'Purchases retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve purchases',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

