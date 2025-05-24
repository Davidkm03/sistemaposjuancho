<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'user_id',
        'tax',
        'discount',
        'total',
        'payment_method',
        'payment_reference',
        'status',
        'notes'
    ];
    
    protected $casts = [
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];
    
    /**
     * Get the customer that owns the sale
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    /**
     * Get the user that owns the sale
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the sale details for the sale
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }
    
    /**
     * Get the accounting transactions for the sale
     */
    public function accountingTransactions(): HasMany
    {
        return $this->hasMany(AccountingTransaction::class);
    }
}
