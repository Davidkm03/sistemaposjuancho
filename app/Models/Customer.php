<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'document_number',
        'document_type',
        'balance',
        'status'
    ];
    
    protected $casts = [
        'balance' => 'decimal:2',
        'status' => 'boolean',
    ];
    
    /**
     * Get the sales for the customer
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
