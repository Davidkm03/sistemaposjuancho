@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Informes Contables</h1>
        <a href="{{ route('accounting.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver a Transacciones
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtrar por Fecha</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('accounting.reports') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Fecha Inicio</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Fecha Fin</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('accounting.reports') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Restablecer
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen de Estado de Resultados -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estado de Resultados ({{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }})</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            <h6 class="font-weight-bold text-success">Ingresos</h6>
                            <h3>${{ number_format($incomeStatement['income'], 2) }}</h3>
                        </div>
                        <div class="col-md-3 text-center mb-4">
                            <h6 class="font-weight-bold text-danger">Gastos</h6>
                            <h3>${{ number_format($incomeStatement['expenses'], 2) }}</h3>
                        </div>
                        <div class="col-md-3 text-center mb-4">
                            <h6 class="font-weight-bold text-info">Ajustes</h6>
                            <h3>${{ number_format($incomeStatement['adjustments'], 2) }}</h3>
                        </div>
                        <div class="col-md-3 text-center mb-4">
                            <h6 class="font-weight-bold">Resultado Neto</h6>
                            <h3 class="{{ $incomeStatement['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                ${{ number_format($incomeStatement['profit'], 2) }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Ingresos Mensuales -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ingresos por Mes</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                    <hr>
                    <div class="small text-muted text-center">Gráfico de ingresos mensuales</div>
                </div>
            </div>
        </div>

        <!-- Desglose por Categoría -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Desglose por Categoría</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie mb-4">
                        <canvas id="categoryBreakdownChart"></canvas>
                    </div>
                    <hr>
                    <div class="small text-muted text-center">Distribución de transacciones por categoría</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Categorías -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Transacciones por Categoría</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Total</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalAmount = $categoryBreakdown->sum('total'); @endphp
                        @forelse($categoryBreakdown as $category)
                            <tr>
                                <td>{{ $category->category ?? 'Sin categoría' }}</td>
                                <td class="{{ $category->total >= 0 ? 'text-success' : 'text-danger' }}">
                                    ${{ number_format($category->total, 2) }}
                                </td>
                                <td>
                                    @if($totalAmount != 0)
                                        {{ number_format(abs($category->total) / abs($totalAmount) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No hay datos disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Preparar datos para el gráfico de ingresos mensuales
    const monthlyRevenueData = {
        labels: {!! json_encode($monthlyRevenue->pluck('month')) !!},
        datasets: [{
            label: 'Ingresos ($)',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            borderColor: 'rgba(78, 115, 223, 1)',
            pointBackgroundColor: 'rgba(78, 115, 223, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
            lineTension: 0.3,
            data: {!! json_encode($monthlyRevenue->pluck('total')) !!},
        }]
    };

    // Configuración del gráfico de ingresos mensuales
    const monthlyRevenueOptions = {
        maintainAspectRatio: false,
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                ticks: {
                    beginAtZero: true
                },
                grid: {
                    color: "rgba(0, 0, 0, 0.05)"
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    };

    // Inicializar el gráfico de ingresos mensuales
    new Chart(
        document.getElementById('monthlyRevenueChart'),
        {
            type: 'line',
            data: monthlyRevenueData,
            options: monthlyRevenueOptions
        }
    );

    // Preparar datos para el gráfico de desglose por categoría
    const categoryData = {
        labels: {!! json_encode($categoryBreakdown->pluck('category')->map(function($item) { return $item ?? 'Sin categoría'; })) !!},
        datasets: [{
            data: {!! json_encode($categoryBreakdown->pluck('total')->map(function($val) { return abs($val); })) !!},
            backgroundColor: [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
                '#5a5c69', '#858796', '#d1d3e2', '#f8f9fc', '#3a3b45'
            ],
            hoverBackgroundColor: [
                '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', 
                '#484a54', '#6e7081', '#b3b5c9', '#d8dbe6', '#2e2f37'
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }]
    };

    // Configuración del gráfico de desglose por categoría
    const categoryOptions = {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                display: true
            }
        },
        cutout: '70%',
    };

    // Inicializar el gráfico de desglose por categoría
    new Chart(
        document.getElementById('categoryBreakdownChart'),
        {
            type: 'doughnut',
            data: categoryData,
            options: categoryOptions
        }
    );
</script>
@endpush
@endsection
