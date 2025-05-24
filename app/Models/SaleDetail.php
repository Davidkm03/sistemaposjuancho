<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleDetail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price',
        'discount',
        'total'
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];
    
    /**
     * Get the sale that owns the sale detail
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
    
    /**
     * Get the product that owns the sale detail
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
