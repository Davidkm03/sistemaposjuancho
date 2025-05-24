@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Editar Meta de Ventas</h1>
        <a href="{{ route('goals.show', $goal) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información de la Meta</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('goals.update.post', $goal) }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Título de la Meta <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $goal->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="target_amount" class="form-label">Monto Objetivo (COP) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="1" class="form-control @error('target_amount') is-invalid @enderror" 
                                   id="target_amount" name="target_amount" value="{{ old('target_amount', $goal->target_amount) }}" required>
                            @error('target_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                               id="start_date" name="start_date" value="{{ old('start_date', $goal->start_date->format('Y-m-d')) }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">Fecha de Finalización <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                               id="end_date" name="end_date" value="{{ old('end_date', $goal->end_date->format('Y-m-d')) }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $goal->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="deduct_expenses" 
                                   name="deduct_expenses" {{ old('deduct_expenses', $goal->deduct_expenses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="deduct_expenses">
                                Descontar gastos del progreso
                            </label>
                            <div class="form-text text-muted">
                                Si se marca, los gastos registrados se descontarán del progreso de la meta.
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', $goal->status) == 'active' ? 'selected' : '' }}>Activa</option>
                            <option value="completed" {{ old('status', $goal->status) == 'completed' ? 'selected' : '' }}>Completada</option>
                            <option value="failed" {{ old('status', $goal->status) == 'failed' ? 'selected' : '' }}>No Alcanzada</option>
                            <option value="cancelled" {{ old('status', $goal->status) == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Progreso actual:</strong> {{ number_format($goal->current_amount, 2) }} ({{ $goal->progress_percentage }}%)
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Actualizar Meta
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-danger text-white">
            <h6 class="m-0 font-weight-bold">Zona de Peligro</h6>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="font-weight-bold">Eliminar Meta</h5>
                    <p class="mb-0 text-muted">Esta acción no se puede deshacer.</p>
                </div>
                <form action="{{ route('goals.destroy', $goal) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta meta? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i> Eliminar Meta
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
