<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountingTransaction;
use App\Models\SalesGoal;
use App\Services\GoalRecommendationService;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Verificar si el usuario tiene permiso
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('accounting.view')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para ver los gastos.');
        }
        
        $query = AccountingTransaction::query();
        
        // Filtrar por tipo (ingreso/egreso)
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        // Filtrar por categoría
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        // Obtener todas las categorías únicas para el filtro
        $categories = AccountingTransaction::distinct('category')->pluck('category');
        
        // Obtener las transacciones paginadas
        $transactions = $query->latest()->paginate(10);
        
        return view('expenses.index', compact('transactions', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verificar si el usuario tiene permiso
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('accounting.create')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para crear nuevos gastos.');
        }
        
        // Obtener categorías para mostrar en el formulario
        $categories = AccountingTransaction::distinct('category')->pluck('category');
        
        return view('expenses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar si el usuario tiene permiso
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('accounting.create')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para crear nuevos gastos'], 403);
            }
            
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para crear nuevos gastos.');
        }
        
        // Validar datos
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            return redirect()->route('expenses.create')
                             ->withErrors($validator)
                             ->withInput();
        }
        
        // Crear la transacción
        $transaction = new AccountingTransaction();
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->type = $request->type;
        $transaction->category = $request->category;
        $transaction->notes = $request->notes;
        $transaction->user_id = auth()->id();
        $transaction->reference = 'POS-' . now()->format('YmdHis');
        $transaction->transaction_date = now();
        $transaction->save();
        
        // Si es un gasto, actualizar las metas que descuenten gastos
        if ($request->type === 'expense') {
            $this->updateSalesGoals();
        }
        
        $typeLabel = $request->type === 'income' ? 'Ingreso' : 'Gasto';
        
        // Responder según el tipo de solicitud
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "$typeLabel registrado correctamente",
                'transaction' => $transaction
            ]);
        }
        
        return redirect()->route('expenses.index')
                         ->with('success', "$typeLabel registrado correctamente.");
    }
    
    /**
     * Actualiza las metas de ventas activas que descuentan gastos
     * 
     * @return void
     */
    protected function updateSalesGoals()
    {
        // Obtener todas las metas activas que descuentan gastos
        $activeGoals = SalesGoal::where('status', 'active')
                      ->where('deduct_expenses', true)
                      ->get();
        
        // Si no hay metas activas, no hacer nada
        if ($activeGoals->isEmpty()) {
            return;
        }
        
        // Usar el servicio para actualizar cada meta
        $recommendationService = app(GoalRecommendationService::class);
        
        foreach ($activeGoals as $goal) {
            $recommendationService->updateGoalProgress($goal);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Verificar si el usuario tiene permiso
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('accounting.view')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para ver los detalles de gastos.');
        }
        
        $transaction = AccountingTransaction::findOrFail($id);
        
        return view('expenses.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Verificar si el usuario tiene permiso
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('accounting.edit')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para editar gastos.');
        }
        
        $transaction = AccountingTransaction::findOrFail($id);
        $categories = AccountingTransaction::distinct('category')->pluck('category');
        
        return view('expenses.edit', compact('transaction', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Verificar si el usuario tiene permiso
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('accounting.edit')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para actualizar gastos.');
        }
        
        // Validar datos
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('expenses.edit', $id)
                             ->withErrors($validator)
                             ->withInput();
        }
        
        // Actualizar la transacción
        $transaction = AccountingTransaction::findOrFail($id);
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->type = $request->type;
        $transaction->category = $request->category;
        $transaction->notes = $request->notes;
        $transaction->save();
        
        $typeLabel = $request->type === 'income' ? 'Ingreso' : 'Gasto';
        
        return redirect()->route('expenses.index')
                         ->with('success', "$typeLabel actualizado correctamente.");
    }

    /**
     * Store a new transaction from POS API request.
     * This method is specifically designed to handle AJAX requests from the POS interface.
     */
    public function storeFromPos(Request $request)
    {
        // Verificar que la solicitud sea JSON
        if (!$request->isJson()) {
            return response()->json(['error' => 'Se requiere una solicitud JSON'], 400);
        }
        
        // Validar datos
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        try {
            // Crear la transacción
            $transaction = new AccountingTransaction();
            $transaction->amount = $request->amount;
            $transaction->description = $request->description;
            $transaction->type = $request->type;
            $transaction->category = $request->category;
            $transaction->notes = $request->notes;
            $transaction->user_id = auth()->id(); // Asociar al usuario actual
            $transaction->reference = 'POS-' . now()->format('YmdHis');
            $transaction->transaction_date = now();
            $transaction->save();
            
            return response()->json([
                'success' => true,
                'message' => $request->type === 'income' ? 'Ingreso registrado correctamente' : 'Gasto registrado correctamente',
                'transaction' => $transaction
            ]);
        } catch (\Exception $e) {
            // Registrar el error para depuración
            \Log::error('Error al guardar transacción desde POS: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la transacción',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Verificar si el usuario tiene permiso
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('accounting.delete')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para eliminar gastos.');
        }
        
        $transaction = AccountingTransaction::findOrFail($id);
        $type = $transaction->type === 'income' ? 'Ingreso' : 'Gasto';
        
        $transaction->delete();
        
        return redirect()->route('expenses.index')
                         ->with('success', "$type eliminado correctamente.");
    }
}
