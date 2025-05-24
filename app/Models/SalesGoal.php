<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesGoal extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title', 'description', 'target_amount', 'current_amount',
        'start_date', 'end_date', 'status', 'deduct_expenses',
        'recommendation_settings', 'user_id'
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'recommendation_settings' => 'array',
        'deduct_expenses' => 'boolean'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function productRecommendations()
    {
        return $this->hasMany(GoalProductRecommendation::class);
    }
    
    public function comboRecommendations()
    {
        return $this->hasMany(GoalComboRecommendation::class);
    }
    
    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->current_amount / $this->target_amount) * 100, 2));
    }
    
    public function getRemainingAmountAttribute()
    {
        return max(0, $this->target_amount - $this->current_amount);
    }
    
    public function getDaysRemainingAttribute()
    {
        return max(0, now()->diffInDays($this->end_date, false));
    }
}
