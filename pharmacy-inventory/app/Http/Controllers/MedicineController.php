<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Medicine::with('category');

            // Filter by category if provided
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by low stock if provided
            if ($request->has('low_stock') && $request->low_stock) {
                $query->lowStock();
            }

            // Filter by expired if provided
            if ($request->has('expired') && $request->expired) {
                $query->expired();
            }

            // Search by name if provided
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $medicines = $query->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'data' => $medicines,
                'message' => 'Medicines retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve medicines',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'quantity' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                'expiry_date' => 'required|date|after:today',
                'category_id' => 'required|exists:categories,id'
            ]);

            $medicine = Medicine::create($validated);

            return response()->json([
                'success' => true,
                'data' => $medicine->load('category'),
                'message' => 'Medicine created successfully'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create medicine',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $medicine = Medicine::with('category')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $medicine,
                'message' => 'Medicine retrieved successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve medicine',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $medicine = Medicine::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'quantity' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                'expiry_date' => 'required|date|after:today',
                'category_id' => 'required|exists:categories,id'
            ]);

            $medicine->update($validated);

            return response()->json([
                'success' => true,
                'data' => $medicine->load('category'),
                'message' => 'Medicine updated successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update medicine',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $medicine = Medicine::findOrFail($id);
            $medicine->delete();

            return response()->json([
                'success' => true,
                'message' => 'Medicine deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete medicine',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get medicines statistics for dashboard.
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalMedicines = Medicine::count();
            $lowStockMedicines = Medicine::lowStock()->count();
            $expiredMedicines = Medicine::expired()->count();
            $totalValue = Medicine::sum(DB::raw('quantity * price'));

            return response()->json([
                'success' => true,
                'data' => [
                    'total_medicines' => $totalMedicines,
                    'low_stock_medicines' => $lowStockMedicines,
                    'expired_medicines' => $expiredMedicines,
                    'total_value' => $totalValue
                ],
                'message' => 'Statistics retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
