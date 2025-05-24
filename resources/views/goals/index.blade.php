@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Metas de Ventas</h1>
        <a href="{{ route('goals.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle mr-1"></i> Nueva Meta
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Metas Activas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-bullseye mr-1"></i> Metas Activas
            </h6>
        </div>
        <div class="card-body">
            @if($activeGoals->count() > 0)
                <div class="row">
                    @foreach($activeGoals as $goal)
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-primary h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $goal->title }}</div>
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Meta: {{ number_format($goal->target_amount, 2) }}
                                            </div>
                                            <div class="mb-2">
                                                <span class="text-muted">Progreso: {{ number_format($goal->current_amount, 2) }} ({{ $goal->progress_percentage }}%)</span>
                                            </div>
                                            <div class="progress mb-2">
                                                <div class="progress-bar {{ $goal->progress_percentage > 66 ? 'bg-success' : ($goal->progress_percentage > 33 ? 'bg-warning' : 'bg-danger') }}" 
                                                     role="progressbar" style="width: {{ $goal->progress_percentage }}%">
                                                </div>
                                            </div>
                                            <div class="text-xs text-muted">
                                                {{ \Carbon\Carbon::parse($goal->end_date)->diffForHumans() }}
                                                ({{ $goal->days_remaining }} días restantes)
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer py-2">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </a>
                                        <a href="{{ route('goals.edit', $goal) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <img src="https://cdn-icons-png.flaticon.com/512/1380/1380641.png" alt="No hay metas activas" style="width: 120px; opacity: 0.5;" class="mb-3">
                    <p class="text-muted">No hay metas activas actualmente</p>
                    <a href="{{ route('goals.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-1"></i> Crear Nueva Meta
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Metas Completadas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-check-circle mr-1"></i> Metas Completadas
            </h6>
        </div>
        <div class="card-body">
            @if($completedGoals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Meta</th>
                                <th>Alcanzado</th>
                                <th>Finalizada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedGoals as $goal)
                                <tr>
                                    <td>{{ $goal->title }}</td>
                                    <td>{{ number_format($goal->target_amount, 2) }}</td>
                                    <td>{{ number_format($goal->current_amount, 2) }}</td>
                                    <td>{{ $goal->updated_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-3">No hay metas completadas.</p>
            @endif
        </div>
    </div>

    <!-- Metas Fallidas -->
    @if(isset($failedGoals) && $failedGoals->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-times-circle mr-1"></i> Metas No Alcanzadas
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Meta</th>
                                <th>Alcanzado</th>
                                <th>% Completado</th>
                                <th>Finalizada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($failedGoals as $goal)
                                <tr>
                                    <td>{{ $goal->title }}</td>
                                    <td>{{ number_format($goal->target_amount, 2) }}</td>
                                    <td>{{ number_format($goal->current_amount, 2) }}</td>
                                    <td>{{ $goal->progress_percentage }}%</td>
                                    <td>{{ $goal->updated_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
