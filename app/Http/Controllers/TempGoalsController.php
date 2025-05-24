<?php

namespace App\Http\Controllers;

use App\Models\SalesGoal;
use App\Models\GoalProductRecommendation;
use App\Models\GoalComboRecommendation;
use App\Services\GoalRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TempGoalsController extends Controller
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
        
        return redirect()->route('temp.goals.show', $goal)
            ->with('success', '¡Meta creada con éxito! Revisa las recomendaciones para alcanzarla.');
    }
    
    /**
     * Muestra los detalles de una meta específica
     */
    public function show($goalId)
    {
        $goal = SalesGoal::findOrFail($goalId);
        
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
    public function edit($goalId)
    {
        $goal = SalesGoal::findOrFail($goalId);
        return view('goals.edit', compact('goal'));
    }
    
    /**
     * Actualiza una meta existente
     */
    public function update(Request $request, $goalId)
    {
        $goal = SalesGoal::findOrFail($goalId);
        
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
        
        return redirect()->route('temp.goals.show', $goal)
            ->with('success', 'Meta actualizada correctamente.');
    }
    
    /**
     * Regenera las recomendaciones para una meta
     */
    public function regenerateRecommendations($goalId)
    {
        $goal = SalesGoal::findOrFail($goalId);
        
        // Actualizar progreso y generar nuevas recomendaciones
        $this->recommendationService->updateGoalProgress($goal);
        $this->recommendationService->generateRecommendations($goal);
        
        return redirect()->route('temp.goals.show', $goal)
            ->with('success', 'Recomendaciones actualizadas basadas en datos recientes.');
    }
}
