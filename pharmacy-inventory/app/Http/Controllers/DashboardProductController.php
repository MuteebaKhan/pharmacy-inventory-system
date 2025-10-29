<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardProductController extends Controller
{
    /**
     * Create a new product from dashboard.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        Product::create($validated);

        return redirect()->route('dashboard')
            ->with('toast_type', 'success')
            ->with('toast_message', 'Product added successfully');
    }

    /**
     * Update an existing product from dashboard.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::findOrFail($id);
        $product->update($validated);

        return redirect()->route('dashboard')
            ->with('toast_type', 'success')
            ->with('toast_message', 'Product updated successfully');
    }

    /**
     * Delete a product from dashboard.
     */
    public function destroy(string $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('dashboard')
            ->with('toast_type', 'success')
            ->with('toast_message', 'Product deleted successfully');
    }
}


