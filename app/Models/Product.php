<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 
        'code', 
        'description', 
        'image', 
        'purchase_price', 
        'selling_price', 
        'stock', 
        'min_stock', 
        'category_id', 
        'supplier_id', 
        'status'
    ];
    
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'status' => 'boolean',
    ];
    
    /**
     * Get the category that owns the product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Get the supplier that owns the product
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    
    /**
     * Determina si el producto tiene stock bajo
     *
     * @return bool
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
    
    /**
     * Get the sale details for the product
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }
    

}
