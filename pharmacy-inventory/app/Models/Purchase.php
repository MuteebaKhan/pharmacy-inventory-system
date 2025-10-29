<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Purchase extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'quantity_purchased',
        'total_cost',
        'purchase_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_cost' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    /**
     * Get the product that owns the purchase.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
