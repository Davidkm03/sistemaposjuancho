<?php
    // Forzamos a ignorar la caché para obtener datos frescos
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
?>

@php
    // Forzamos una consulta fresca directamente a la base de datos
    \DB::enableQueryLog(); // Para debugging
    
    // Utilizamos el servicio para asegurarnos de que tengamos datos actualizados
    $goalService = app(\App\Services\GoalRecommendationService::class);
    
    // Consulta directa simple para obtener la meta activa (con fresh para ignorar caché)
    $activeGoal = \App\Models\SalesGoal::where('status', 'active')
                    ->orderBy('end_date', 'asc')
                    ->first();
                    
    // Si hay una meta activa, actualizamos su progreso usando el servicio
    if ($activeGoal) {
        // Actualizar progreso usando el servicio que calcula ventas y gastos correctamente
        // Consultamos las ventas y transacciones en tiempo real (ignorando caché)
        $activeGoal = $goalService->updateGoalProgress($activeGoal);
        
        // Calcular porcentaje de progreso (mostrando valores negativos para reflejar el impacto de los gastos)
        $progressPercentage = $activeGoal->target_amount > 0
            ? round(($activeGoal->current_amount / $activeGoal->target_amount) * 100, 2)
            : 0;
        
        // Para la barra de progreso, usamos un mínimo de 0%
        $progressBarPercentage = max(0, $progressPercentage);
        
        // Calcular días restantes
        $daysRemaining = max(0, now()->diffInDays($activeGoal->end_date, false));
    }
    
    // Registrar las consultas SQL ejecutadas (para debugging)
    $queries = \DB::getQueryLog();
    // Descomentar para debug: print_r($queries);

@endphp

@if(isset($activeGoal))
<div data-goal-id="{{ $activeGoal->id }}">
    <h5>{{ $activeGoal->title }}</h5>
    
    <div class="d-flex justify-content-between mb-1">
        <span>Progreso:</span>
        <span class="font-weight-bold">
            <span class="{{ $activeGoal->current_amount < 0 ? 'text-danger' : '' }}">
                {{ number_format($activeGoal->current_amount, 2) }}
            </span> / {{ number_format($activeGoal->target_amount, 2) }}
        </span>
    </div>
    
    <div class="progress mb-3">
        <div class="progress-bar {{ $progressPercentage > 66 ? 'bg-success' : ($progressPercentage > 33 ? 'bg-warning' : 'bg-danger') }}" 
             role="progressbar" style="width: {{ $progressBarPercentage }}%">
            {{ $progressPercentage }}%
        </div>
    </div>
    
    @if($activeGoal->current_amount < 0)
    <div class="alert alert-warning small mb-2">
        <i class="fas fa-exclamation-triangle me-1"></i>
        Los gastos superan a las ventas por <strong>{{ number_format(abs($activeGoal->current_amount), 2) }}</strong>
    </div>
    @endif
    
    <div class="d-flex justify-content-between text-muted small">
        <span>Faltan: {{ $daysRemaining }} días</span>
        <span>Meta: {{ number_format($activeGoal->target_amount, 2) }}</span>
    </div>
    
    <div class="mt-3">
        <div class="alert alert-info mb-0">
            <i class="fas fa-lightbulb me-1"></i>
            Para ver recomendaciones detalladas, accede al panel de metas desde el menú de administración.
        </div>
    </div>
</div>
@else
<div class="text-center py-3">
    <i class="fas fa-bullseye text-muted" style="font-size: 3rem;"></i>
    <p class="mt-2">No hay metas activas actualmente</p>
    <a href="{{ route('goals.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i> Crear Meta
    </a>
</div>
@endif
