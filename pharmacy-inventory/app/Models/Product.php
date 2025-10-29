<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Sale;
use App\Models\Purchase;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'category_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Check if the product is low in stock.
     */
    public function isLowStock(int $threshold = 10): bool
    {
        return $this->quantity < $threshold;
    }

    /**
     * Scope for low stock products.
     */
    public function scopeLowStock($query, int $threshold = 10)
    {
        return $query->where('quantity', '<', $threshold);
    }

    /**
     * Scope for products by category.
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for searching products by name.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }

    /**
     * Get all sales for the product.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get all purchases for the product.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
