<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SalesGoal;
use App\Models\AccountingTransaction;
use App\Services\GoalRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiagnosticController extends Controller
{
    protected $recommendationService;
    
    public function __construct(GoalRecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
        $this->middleware('auth');
    }
    
    /**
     * Diagnóstico del sistema de metas
     */
    public function diagnoseGoals()
    {
        // Solo administradores
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Acceso no autorizado']);
        }
        
        $results = [];
        
        // 1. Verificar metas activas
        $activeGoals = SalesGoal::where('status', 'active')->get();
        $results['active_goals_count'] = $activeGoals->count();
        
        if ($activeGoals->isEmpty()) {
            return response()->json([
                'message' => 'No hay metas activas para diagnosticar',
                'results' => $results
            ]);
        }
        
        // 2. Analizar cada meta
        $goalData = [];
        foreach ($activeGoals as $goal) {
            // Calcular ventas de forma explícita
            $sales = Sale::whereBetween('created_at', [$goal->start_date, $goal->end_date])
                ->select(DB::raw('SUM(total) as total_sales'), DB::raw('COUNT(*) as sales_count'))
                ->first();
                
            // Calcular gastos si es necesario
            $expenses = null;
            if ($goal->deduct_expenses) {
                $expenses = AccountingTransaction::where('type', 'expense')
                    ->whereBetween('created_at', [$goal->start_date, $goal->end_date])
                    ->select(DB::raw('SUM(amount) as total_expenses'), DB::raw('COUNT(*) as expenses_count'))
                    ->first();
            }
            
            // Forzar la actualización de la meta
            $updatedGoal = $this->recommendationService->updateGoalProgress($goal);
            
            // Recolectar resultados
            $goalData[] = [
                'id' => $goal->id,
                'title' => $goal->title,
                'target_amount' => $goal->target_amount,
                'current_amount' => $goal->current_amount,
                'updated_amount' => $updatedGoal->current_amount,
                'start_date' => $goal->start_date->format('Y-m-d'),
                'end_date' => $goal->end_date->format('Y-m-d'),
                'status' => $goal->status,
                'days_remaining' => $goal->days_remaining,
                'progress_percentage' => $goal->progress_percentage,
                'deduct_expenses' => $goal->deduct_expenses,
                'sales_data' => [
                    'total_sales' => $sales->total_sales ?? 0,
                    'sales_count' => $sales->sales_count ?? 0,
                ],
                'expenses_data' => $expenses ? [
                    'total_expenses' => $expenses->total_expenses ?? 0,
                    'expenses_count' => $expenses->expenses_count ?? 0,
                ] : null
            ];
        }
        
        $results['goals'] = $goalData;
        
        // 3. Verificar ventas recientes
        $recentSales = Sale::latest()->take(5)->get();
        $salesData = [];
        
        foreach ($recentSales as $sale) {
            $salesData[] = [
                'id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'total' => $sale->total,
                'status' => $sale->status,
                'created_at' => $sale->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        $results['recent_sales'] = $salesData;
        
        // 4. Retornar diagnóstico
        return response()->json([
            'message' => 'Diagnóstico completado',
            'results' => $results
        ]);
    }
    
    /**
     * Forzar actualización de metas
     */
    public function forceUpdateGoals()
    {
        // Solo administradores
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Acceso no autorizado']);
        }
        
        // Obtener todas las metas activas
        $activeGoals = SalesGoal::where('status', 'active')->get();
        
        if ($activeGoals->isEmpty()) {
            return response()->json([
                'message' => 'No hay metas activas para actualizar'
            ]);
        }
        
        $updated = [];
        
        DB::beginTransaction();
        try {
            foreach ($activeGoals as $goal) {
                // Calcular ventas directo en DB para evitar cualquier caché
                $sales = DB::table('sales')
                    ->whereBetween('created_at', [$goal->start_date, $goal->end_date])
                    ->sum('total');
                
                // Calcular gastos si es necesario
                $expenses = 0;
                if ($goal->deduct_expenses) {
                    $expenses = DB::table('accounting_transactions')
                        ->where('type', 'expense')
                        ->whereBetween('created_at', [$goal->start_date, $goal->end_date])
                        ->sum('amount');
                }
                
                // Actualizar meta manualmente
                $currentAmount = max(0, $sales - ($goal->deduct_expenses ? $expenses : 0));
                
                // Guardar la meta con los nuevos valores
                $oldAmount = $goal->current_amount;
                $goal->current_amount = $currentAmount;
                
                // Actualizar estado si es necesario
                if ($goal->current_amount >= $goal->target_amount) {
                    $goal->status = 'completed';
                } elseif ($goal->end_date < now() && $goal->status == 'active') {
                    $goal->status = 'failed';
                }
                
                $goal->save();
                
                $updated[] = [
                    'id' => $goal->id,
                    'title' => $goal->title,
                    'old_amount' => $oldAmount,
                    'new_amount' => $goal->current_amount,
                    'sales_total' => $sales,
                    'expenses_total' => $expenses
                ];
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Metas actualizadas manualmente con éxito',
                'updated_goals' => $updated
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'error' => 'Error al actualizar metas: ' . $e->getMessage()
            ], 500);
        }
    }
}
