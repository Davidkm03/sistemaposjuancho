@if(isset($activeGoal))
<div data-goal-id="{{ $activeGoal->id }}">
    <h5>{{ $activeGoal->title }}</h5>
    
    <div class="d-flex justify-content-between mb-1">
        <span>Progreso:</span>
        <span class="font-weight-bold">{{ number_format($activeGoal->current_amount, 2) }} / {{ number_format($activeGoal->target_amount, 2) }}</span>
    </div>
    
    <div class="progress mb-3">
        <div class="progress-bar {{ $activeGoal->progress_percentage > 66 ? 'bg-success' : ($activeGoal->progress_percentage > 33 ? 'bg-warning' : 'bg-danger') }}" 
             role="progressbar" style="width: {{ $activeGoal->progress_percentage }}%">
            {{ $activeGoal->progress_percentage }}%
        </div>
    </div>
    
    <div class="d-flex justify-content-between text-muted small">
        <span>Faltan: {{ $activeGoal->days_remaining }} días</span>
        <span>Meta: {{ number_format($activeGoal->target_amount, 2) }}</span>
    </div>
    
    <div class="mt-3">
        <h6 class="font-weight-bold">Recomendación rápida:</h6>
        @if($activeGoal->comboRecommendations->count() > 0)
            <div class="alert alert-success mb-0">
                <i class="fas fa-magic mr-1"></i>
                <strong>Combo sugerido:</strong> {{ $activeGoal->comboRecommendations->first()->combo_name }}
                <br>
                <small>Ganancia estimada: {{ number_format($activeGoal->comboRecommendations->first()->expected_profit, 2) }}</small>
            </div>
        @elseif($activeGoal->productRecommendations->count() > 0)
            <div class="alert alert-info mb-0">
                <i class="fas fa-tag mr-1"></i>
                <strong>Producto sugerido:</strong> {{ $activeGoal->productRecommendations->first()->product->name }}
                <br>
                <small>Cantidad: {{ $activeGoal->productRecommendations->first()->recommended_quantity }} unidades</small>
            </div>
        @else
            <div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-circle mr-1"></i>
                No hay recomendaciones disponibles.
            </div>
        @endif
    </div>
</div>
@else
<div class="text-center py-3">
    <i class="fas fa-bullseye text-muted" style="font-size: 3rem;"></i>
    <p class="mt-2">No hay metas activas actualmente</p>
    <a href="{{ route('goals.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus mr-1"></i> Crear Meta
    </a>
</div>
@endif
