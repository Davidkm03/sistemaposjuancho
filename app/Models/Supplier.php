<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
        'tax_number',
        'status'
    ];
    
    protected $casts = [
        'status' => 'boolean',
    ];
    
    /**
     * Get the products for the supplier
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
