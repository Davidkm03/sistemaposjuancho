@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reportes Financieros</h1>
        <div>
            <button type="button" class="btn btn-success" id="exportBtn">
                <i class="fas fa-file-excel mr-1"></i> Exportar Excel
            </button>
            <button type="button" class="btn btn-secondary" id="printBtn">
                <i class="fas fa-print mr-1"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Filtros de Reporte -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Reporte</h6>
        </div>
        <div class="card-body">
            <form id="reportForm" action="{{ route('reports.financial') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="report_type" class="form-label">Tipo de Reporte</label>
                    <select class="form-select" id="report_type" name="report_type">
                        <option value="income_expense" {{ request('report_type') == 'income_expense' ? 'selected' : '' }}>Ingresos y Gastos</option>
                        <option value="profit_loss" {{ request('report_type') == 'profit_loss' ? 'selected' : '' }}>Ganancias y Pérdidas</option>
                        <option value="sales_summary" {{ request('report_type') == 'sales_summary' ? 'selected' : '' }}>Resumen de Ventas</option>
                        <option value="expense_summary" {{ request('report_type') == 'expense_summary' ? 'selected' : '' }}>Resumen de Gastos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="period" class="form-label">Período</label>
                    <select class="form-select" id="period" name="period">
                        <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Diario</option>
                        <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                        <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Mensual</option>
                        <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Anual</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Personalizado</option>
                    </select>
                </div>
                <div class="col-md-3 date-range {{ request('period') == 'custom' ? '' : 'd-none' }}">
                    <label for="from_date" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date', date('Y-m-01')) }}">
                </div>
                <div class="col-md-3 date-range {{ request('period') == 'custom' ? '' : 'd-none' }}">
                    <label for="to_date" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen Financiero -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Ingresos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($totalIncome, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Gastos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($totalExpense, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Ganancias Netas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($totalIncome - $totalExpense, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Margen de Beneficio
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ $totalIncome > 0 ? number_format(($totalIncome - $totalExpense) / $totalIncome * 100, 1) : 0 }}%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                             style="width: {{ $totalIncome > 0 ? min(100, max(0, (($totalIncome - $totalExpense) / $totalIncome * 100))) : 0 }}%" 
                                             aria-valuenow="{{ $totalIncome > 0 ? (($totalIncome - $totalExpense) / $totalIncome * 100) : 0 }}" 
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ingresos vs Gastos ({{ $periodLabel }})</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="incomeExpenseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribución de Gastos</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="expensesPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($expenseCategories as $category => $color)
                            <span class="mr-2">
                                <i class="fas fa-circle" style="color: {{ $color }}"></i> {{ ucfirst($category) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Detalle -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $reportTitle }}</h6>
        </div>
        <div class="card-body">
            @if(request('report_type') == 'income_expense' || request('report_type') == 'profit_loss')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>Período</th>
                                <th class="text-right">Ingresos</th>
                                <th class="text-right">Gastos</th>
                                <th class="text-right">Ganancias</th>
                                <th>Margen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData as $item)
                                <tr>
                                    <td>{{ $item['period'] }}</td>
                                    <td class="text-right text-success">${{ number_format($item['income'], 2) }}</td>
                                    <td class="text-right text-danger">${{ number_format($item['expense'], 2) }}</td>
                                    <td class="text-right font-weight-bold {{ $item['profit'] < 0 ? 'text-danger' : 'text-primary' }}">
                                        ${{ number_format($item['profit'], 2) }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-sm flex-grow-1 mr-2" style="height: 6px;">
                                                <div class="progress-bar {{ $item['margin'] < 0 ? 'bg-danger' : 'bg-success' }}" 
                                                     role="progressbar" 
                                                     style="width: {{ min(100, max(0, abs($item['margin']))) }}%" 
                                                     aria-valuenow="{{ $item['margin'] }}" 
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span>{{ number_format($item['margin'], 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                                        <p>No hay datos disponibles para el período seleccionado</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td>TOTAL</td>
                                <td class="text-right text-success">${{ number_format($totalIncome, 2) }}</td>
                                <td class="text-right text-danger">${{ number_format($totalExpense, 2) }}</td>
                                <td class="text-right {{ ($totalIncome - $totalExpense) < 0 ? 'text-danger' : 'text-primary' }}">
                                    ${{ number_format($totalIncome - $totalExpense, 2) }}
                                </td>
                                <td>
                                    {{ $totalIncome > 0 ? number_format(($totalIncome - $totalExpense) / $totalIncome * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @elseif(request('report_type') == 'sales_summary')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>Período</th>
                                <th class="text-center">Ventas</th>
                                <th class="text-right">Subtotal</th>
                                <th class="text-right">Impuestos</th>
                                <th class="text-right">Descuentos</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData as $item)
                                <tr>
                                    <td>{{ $item['period'] }}</td>
                                    <td class="text-center">{{ $item['count'] }}</td>
                                    <td class="text-right">${{ number_format($item['subtotal'], 2) }}</td>
                                    <td class="text-right">${{ number_format($item['tax'], 2) }}</td>
                                    <td class="text-right">${{ number_format($item['discount'], 2) }}</td>
                                    <td class="text-right font-weight-bold">${{ number_format($item['total'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                                        <p>No hay datos disponibles para el período seleccionado</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td>TOTAL</td>
                                <td class="text-center">{{ $salesCount }}</td>
                                <td class="text-right">${{ number_format($salesSubtotal, 2) }}</td>
                                <td class="text-right">${{ number_format($salesTax, 2) }}</td>
                                <td class="text-right">${{ number_format($salesDiscount, 2) }}</td>
                                <td class="text-right">${{ number_format($totalIncome, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @elseif(request('report_type') == 'expense_summary')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>Categoría</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-right">Total</th>
                                <th>Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData as $item)
                                <tr>
                                    <td>{{ ucfirst($item['category']) }}</td>
                                    <td class="text-center">{{ $item['count'] }}</td>
                                    <td class="text-right">${{ number_format($item['amount'], 2) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-sm flex-grow-1 mr-2" style="height: 6px;">
                                                <div class="progress-bar bg-danger" 
                                                     role="progressbar" 
                                                     style="width: {{ $item['percentage'] }}%" 
                                                     aria-valuenow="{{ $item['percentage'] }}" 
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span>{{ number_format($item['percentage'], 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                                        <p>No hay datos disponibles para el período seleccionado</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td>TOTAL</td>
                                <td class="text-center">{{ $expenseCount }}</td>
                                <td class="text-right">${{ number_format($totalExpense, 2) }}</td>
                                <td>100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar/ocultar campos de fecha personalizada
        const periodSelect = document.getElementById('period');
        const dateRangeFields = document.querySelectorAll('.date-range');
        
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                dateRangeFields.forEach(field => field.classList.remove('d-none'));
            } else {
                dateRangeFields.forEach(field => field.classList.add('d-none'));
            }
        });
        
        // Configuración del gráfico de ingresos vs gastos
        const incomeExpenseData = @json($chartData);
        const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
        const incomeExpenseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: incomeExpenseData.labels,
                datasets: [
                    {
                        label: 'Ingresos',
                        backgroundColor: 'rgba(78, 115, 223, 0.7)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1,
                        data: incomeExpenseData.income,
                    },
                    {
                        label: 'Gastos',
                        backgroundColor: 'rgba(231, 74, 59, 0.7)',
                        borderColor: 'rgba(231, 74, 59, 1)',
                        borderWidth: 1,
                        data: incomeExpenseData.expense,
                    }
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '$' + context.parsed.y.toLocaleString();
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
        
        // Configuración del gráfico de distribución de gastos
        const expenseData = @json($pieChartData);
        const ctxPie = document.getElementById('expensesPieChart').getContext('2d');
        const expensesPieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: expenseData.labels,
                datasets: [{
                    data: expenseData.data,
                    backgroundColor: expenseData.colors,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '$' + context.raw.toLocaleString() + ' (' + 
                                        ((context.raw / expenseData.data.reduce((a, b) => a + b, 0)) * 100).toFixed(1) + '%)';
                                return label;
                            }
                        }
                    }
                }
            },
        });
        
        // Exportar a Excel
        document.getElementById('exportBtn').addEventListener('click', function() {
            const form = document.getElementById('reportForm');
            const formAction = form.action;
            form.action = formAction + '/export';
            form.submit();
            form.action = formAction;
        });
        
        // Imprimir reporte
        document.getElementById('printBtn').addEventListener('click', function() {
            const form = document.getElementById('reportForm');
            const formAction = form.action;
            form.action = formAction + '/print';
            form.target = '_blank';
            form.submit();
            form.action = formAction;
            form.target = '';
        });
    });
</script>
@endpush
