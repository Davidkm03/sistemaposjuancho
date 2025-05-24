<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalProductRecommendation extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sales_goal_id', 'product_id', 'recommended_quantity', 
        'expected_revenue', 'priority'
    ];
    
    public function salesGoal()
    {
        return $this->belongsTo(SalesGoal::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
