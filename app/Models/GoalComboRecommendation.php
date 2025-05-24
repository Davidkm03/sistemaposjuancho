<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalComboRecommendation extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sales_goal_id', 'combo_name', 'combo_description', 
        'combo_price', 'expected_profit', 'priority'
    ];
    
    public function salesGoal()
    {
        return $this->belongsTo(SalesGoal::class);
    }
    
    public function products()
    {
        return $this->hasMany(GoalComboProduct::class);
    }
}
