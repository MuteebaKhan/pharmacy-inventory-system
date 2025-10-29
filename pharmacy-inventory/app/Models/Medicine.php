<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Medicine extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'quantity',
        'price',
        'expiry_date',
        'category_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expiry_date' => 'date',
        'price' => 'decimal:2',
    ];

    /**
     * Get the category that owns the medicine.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Check if the medicine is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date < Carbon::today();
    }

    /**
     * Check if the medicine is low in stock.
     */
    public function isLowStock(int $threshold = 10): bool
    {
        return $this->quantity < $threshold;
    }

    /**
     * Scope for low stock medicines.
     */
    public function scopeLowStock($query, int $threshold = 10)
    {
        return $query->where('quantity', '<', $threshold);
    }

    /**
     * Scope for expired medicines.
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', Carbon::today());
    }
}
