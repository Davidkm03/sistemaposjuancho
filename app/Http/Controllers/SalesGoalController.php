<?php

namespace App\Http\Controllers;

use App\Models\SalesGoal;
use App\Models\GoalComboRecommendation;
use App\Services\GoalRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesGoalController extends Controller
{
    protected $recommendationService;
    
    public function __construct(GoalRecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
        $this->middleware('auth');
    }
    
    /**
     * Muestra el listado de metas
     */
    public function index()
    {
        // Verificar que sea administrador
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta sección. Se requiere rol de Administrador.');
        }
        $activeGoals = SalesGoal::where('status', 'active')->latest()->get();
        $completedGoals = SalesGoal::where('status', 'completed')->latest()->limit(5)->get();
        $failedGoals = SalesGoal::where('status', 'failed')->latest()->limit(5)->get();
        
        return view('goals.index', compact('activeGoals', 'completedGoals', 'failedGoals'));
    }
    
    /**
     * Muestra el formulario para crear una nueva meta
     */
    public function create()
    {
        // Verificar que sea administrador
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta sección. Se requiere rol de Administrador.');
        }
        return view('goals.create');
    }
    
    /**
     * Almacena una nueva meta
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'deduct_expenses' => 'boolean'
        ]);
        
        $goal = SalesGoal::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'target_amount' => $validated['target_amount'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'deduct_expenses' => $request->has('deduct_expenses'),
            'user_id' => auth()->id(),
            'status' => 'active',
            'current_amount' => 0,
        ]);
        
        // Generar recomendaciones iniciales
        $this->recommendationService->generateRecommendations($goal);
        
        return redirect()->route('goals.show', $goal)
            ->with('success', '¡Meta creada con éxito! Revisa las recomendaciones para alcanzarla.');
    }
    
    /**
     * Muestra los detalles de una meta específica
     */
    public function show(SalesGoal $goal)
    {
        // Verificar que sea administrador
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta sección. Se requiere rol de Administrador.');
        }
        // Actualizar progreso actual
        $this->recommendationService->updateGoalProgress($goal);
        
        // Cargar recomendaciones
        $goal->load([
            'productRecommendations.product',
            'comboRecommendations.products.product'
        ]);
        
        return view('goals.show', compact('goal'));
    }
    
    /**
     * Muestra el formulario para editar una meta
     */
    public function edit(SalesGoal $goal)
    {
        // Verificar que sea administrador
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta sección. Se requiere rol de Administrador.');
        }
        return view('goals.edit', compact('goal'));
    }
    
    /**
     * Actualiza una meta existente
     */
    public function update(Request $request, SalesGoal $goal)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'deduct_expenses' => 'boolean',
            'status' => 'required|in:active,completed,failed,cancelled'
        ]);
        
        $goal->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'target_amount' => $validated['target_amount'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'deduct_expenses' => $request->has('deduct_expenses'),
            'status' => $validated['status']
        ]);
        
        return redirect()->route('goals.show', $goal)
            ->with('success', 'Meta actualizada correctamente.');
    }
    
    /**
     * Elimina una meta
     */
    public function destroy(SalesGoal $goal)
    {
        // Verificar que sea administrador
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta sección. Se requiere rol de Administrador.');
        }
        $goal->delete();
        
        return redirect()->route('goals.index')
            ->with('success', 'Meta eliminada correctamente.');
    }
    
    /**
     * Almacena una nueva meta directamente (ruta alternativa)
     */
    public function storeDirect(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'deduct_expenses' => 'nullable'
        ]);
        
        $goal = SalesGoal::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'target_amount' => $validated['target_amount'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'deduct_expenses' => $request->has('deduct_expenses'),
            'user_id' => auth()->id(),
            'status' => 'active',
            'current_amount' => 0,
        ]);
        
        // Generar recomendaciones iniciales
        $this->recommendationService->generateRecommendations($goal);
        
        return redirect()->route('goals.show', $goal)
            ->with('success', '¡Meta creada con éxito! Revisa las recomendaciones para alcanzarla.');
    }
    
    /**
     * Regenera las recomendaciones para una meta
     */
    public function regenerateRecommendations(SalesGoal $goal)
    {
        // Verificar que sea administrador
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta sección. Se requiere rol de Administrador.');
        }
        // Actualizar progreso y generar nuevas recomendaciones
        $this->recommendationService->updateGoalProgress($goal);
        $this->recommendationService->generateRecommendations($goal);
        
        return redirect()->route('goals.show', $goal)
            ->with('success', 'Recomendaciones actualizadas basadas en datos recientes.');
    }
    
    /**
     * Widget simplificado para mostrar en el POS
     */
    public function posWidget()
    {
        try {
            // Simplificamos al máximo para evitar errores
            return view('goals.simplified-widget');
        } catch (\Exception $e) {
            // Registrar el error para depuración
            \Log::error('Error en posWidget: ' . $e->getMessage());
            
            // Devolver una respuesta básica para evitar que se quede cargando
            return '<div class="alert alert-warning"><i class="fas fa-exclamation-circle me-1"></i> No se pudo cargar la meta actual.</div>';
        }
    }
    
    /**
     * Obtiene los productos de un combo para el POS
     */
    public function posRecommendations($comboId)
    {
        $combo = GoalComboRecommendation::with('products.product')->findOrFail($comboId);
        
        $products = [];
        foreach ($combo->products as $comboProduct) {
            $products[] = [
                'product_id' => $comboProduct->product_id,
                'quantity' => $comboProduct->quantity,
                'name' => $comboProduct->product->name,
                'price' => $comboProduct->product->selling_price
            ];
        }
        
        return response()->json([
            'combo' => $combo->only(['id', 'combo_name', 'combo_price']),
            'products' => $products
        ]);
    }
}
