<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingTransaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'type',
        'reference',
        'amount',
        'sale_id',
        'user_id',
        'description',
        'category',
        'notes',
        'created_by',
        'transaction_date'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];
    
    /**
     * Get the sale that owns the accounting transaction
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
    
    /**
     * Get the user that owns the accounting transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the user that created the accounting transaction
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
