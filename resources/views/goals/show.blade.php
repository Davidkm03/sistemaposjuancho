@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalles de Meta</h1>
        <div>
            <a href="{{ route('goals.regenerate', $goal) }}" class="btn btn-info">
                <i class="fas fa-sync-alt mr-1"></i> Regenerar Recomendaciones
            </a>
            <a href="{{ route('goals.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <!-- Tarjeta de información de la meta -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bullseye mr-1"></i> {{ $goal->title }}
                    </h6>
                    <span class="badge {{ $goal->status === 'active' ? 'bg-primary' : ($goal->status === 'completed' ? 'bg-success' : 'bg-danger') }}">
                        {{ ucfirst($goal->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="h1 text-primary mb-0">{{ number_format($goal->current_amount, 2) }}</div>
                        <div class="text-muted">de {{ number_format($goal->target_amount, 2) }}</div>
                        
                        <div class="mt-3 position-relative pt-3">
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar {{ $goal->progress_percentage > 66 ? 'bg-success' : ($goal->progress_percentage > 33 ? 'bg-warning' : 'bg-danger') }}" 
                                     role="progressbar" style="width: {{ $goal->progress_percentage }}%">
                                    {{ $goal->progress_percentage }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="font-weight-bold text-primary">Información</h6>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Periodo:</span>
                            <span>{{ \Carbon\Carbon::parse($goal->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($goal->end_date)->format('d/m/Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Días restantes:</span>
                            <span>{{ $goal->days_remaining }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Falta por alcanzar:</span>
                            <span class="font-weight-bold">{{ number_format($goal->remaining_amount, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Descuenta gastos:</span>
                            <span>{{ $goal->deduct_expenses ? 'Sí' : 'No' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Creada por:</span>
                            <span>{{ $goal->user->name }}</span>
                        </li>
                    </ul>
                    
                    @if($goal->description)
                        <h6 class="font-weight-bold text-primary">Descripción</h6>
                        <p class="mb-4">{{ $goal->description }}</p>
                    @endif
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('goals.edit', $goal) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-1"></i> Editar Meta
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <!-- Sección de recomendaciones -->
            <div id="recommendations-section">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-lightbulb mr-1"></i> Plan para Alcanzar tu Meta
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Resumen -->
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle mr-1"></i> Resumen de la Meta</h5>
                            <ul class="mb-0">
                                <li>Meta: <strong>{{ number_format($goal->target_amount, 2) }}</strong></li>
                                <li>Progreso actual: <strong>{{ number_format($goal->current_amount, 2) }} ({{ $goal->progress_percentage }}%)</strong></li>
                                <li>Falta por alcanzar: <strong>{{ number_format($goal->remaining_amount, 2) }}</strong></li>
                                <li>Tiempo restante: <strong>{{ $goal->days_remaining }} días</strong></li>
                            </ul>
                        </div>
                        
                        <!-- Combos Recomendados -->
                        <h5 class="mt-4 mb-3"><i class="fas fa-cubes mr-1"></i> Combos Sugeridos</h5>
                        
                        @if($goal->comboRecommendations->count() > 0)
                            <div class="row">
                                @foreach($goal->comboRecommendations->take(3) as $combo)
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-header bg-gradient-primary text-white">
                                                <h6 class="mb-0">{{ $combo->combo_name }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">{{ $combo->combo_description }}</p>
                                                <ul class="list-group list-group-flush mb-3">
                                                    @foreach($combo->products as $comboProduct)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $comboProduct->product->name }}
                                                            <span class="badge bg-primary">x{{ $comboProduct->quantity }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <div class="d-flex justify-content-between">
                                                    <span>Precio: <strong>{{ number_format($combo->combo_price, 2) }}</strong></span>
                                                    <span>Ganancia: <strong>{{ number_format($combo->expected_profit, 2) }}</strong></span>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <a href="{{ route('pos') }}" class="btn btn-sm btn-outline-primary btn-block">
                                                    <i class="fas fa-shopping-cart mr-1"></i> Vender Ahora
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                No hay combos recomendados disponibles.
                            </div>
                        @endif
                        
                        <!-- Productos Individuales Recomendados -->
                        <h5 class="mt-4 mb-3"><i class="fas fa-tags mr-1"></i> Productos Recomendados</h5>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad Sugerida</th>
                                        <th>Ingresos Estimados</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($goal->productRecommendations->take(10) as $recommendation)
                                        <tr>
                                            <td>{{ $recommendation->product->name }}</td>
                                            <td>{{ number_format($recommendation->product->selling_price, 2) }}</td>
                                            <td>{{ $recommendation->recommended_quantity }}</td>
                                            <td>{{ number_format($recommendation->expected_revenue, 2) }}</td>
                                            <td>
                                                <a href="{{ route('pos') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-shopping-cart"></i> Vender
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No hay productos recomendados disponibles.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Estrategias de Venta -->
                        <h5 class="mt-4 mb-3"><i class="fas fa-chart-line mr-1"></i> Estrategias para Alcanzar la Meta</h5>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card border-left-primary h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Venta Cruzada</h5>
                                        <p class="card-text">Ofrece productos complementarios junto con las ventas principales para aumentar el valor del ticket.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-left-success h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Promociones Temporales</h5>
                                        <p class="card-text">Crea ofertas por tiempo limitado para productos de alto margen y aumenta el volumen de ventas.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-left-info h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Fidelización</h5>
                                        <p class="card-text">Contacta a clientes recurrentes y ofréceles descuentos especiales en nuevas compras.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
