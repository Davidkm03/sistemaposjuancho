<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalComboProduct extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'goal_combo_recommendation_id', 'product_id', 'quantity'
    ];
    
    public function combo()
    {
        return $this->belongsTo(GoalComboRecommendation::class, 'goal_combo_recommendation_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
