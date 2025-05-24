@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reportes de Inventario</h1>
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
            <form id="reportForm" action="{{ route('reports.inventory') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="report_type" class="form-label">Tipo de Reporte</label>
                    <select class="form-select" id="report_type" name="report_type">
                        <option value="stock_status" {{ request('report_type') == 'stock_status' ? 'selected' : '' }}>Estado de Stock</option>
                        <option value="low_stock" {{ request('report_type') == 'low_stock' ? 'selected' : '' }}>Productos con Bajo Stock</option>
                        <option value="stock_movement" {{ request('report_type') == 'stock_movement' ? 'selected' : '' }}>Movimientos de Stock</option>
                        <option value="product_valuation" {{ request('report_type') == 'product_valuation' ? 'selected' : '' }}>Valoración de Inventario</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category_id" class="form-label">Categoría</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="supplier_id" class="form-label">Proveedor</label>
                    <select class="form-select" id="supplier_id" name="supplier_id">
                        <option value="">Todos los proveedores</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="stock_status" class="form-label">Estado de Stock</label>
                    <select class="form-select" id="stock_status" name="stock_status">
                        <option value="">Todos</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>En Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Bajo Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Sin Stock</option>
                    </select>
                </div>
                <div class="col-md-3 date-range {{ request('report_type') == 'stock_movement' ? '' : 'd-none' }}">
                    <label for="from_date" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date', date('Y-m-01')) }}">
                </div>
                <div class="col-md-3 date-range {{ request('report_type') == 'stock_movement' ? '' : 'd-none' }}">
                    <label for="to_date" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label for="sort_by" class="form-label">Ordenar por</label>
                    <select class="form-select" id="sort_by" name="sort_by">
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nombre</option>
                        <option value="stock" {{ request('sort_by') == 'stock' ? 'selected' : '' }}>Stock</option>
                        <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Precio</option>
                        <option value="value" {{ request('sort_by') == 'value' ? 'selected' : '' }}>Valor</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen de Inventario -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Productos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalProducts }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Productos en Stock
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $inStockProducts }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Productos con Bajo Stock
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $lowStockProducts }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                Valor Total del Inventario
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($inventoryValue, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribución de Stock por Categoría</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="categoryPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Productos por Valor</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="productValueChart"></canvas>
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
            @if(request('report_type', 'stock_status') == 'stock_status' || request('report_type') == 'low_stock' || request('report_type') == 'product_valuation')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Proveedor</th>
                                <th class="text-right">Precio Compra</th>
                                <th class="text-right">Precio Venta</th>
                                <th class="text-center">Stock</th>
                                <th class="text-right">Valor Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>{{ $product->code }}</td>
                                    <td>
                                        <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                                    </td>
                                    <td>{{ $product->category->name ?? 'Sin categoría' }}</td>
                                    <td>{{ $product->supplier->name ?? 'Sin proveedor' }}</td>
                                    <td class="text-right">${{ number_format($product->purchase_price, 2) }}</td>
                                    <td class="text-right">${{ number_format($product->selling_price, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $product->stock == 0 ? 'danger' : ($product->isLowStock() ? 'warning' : 'success') }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="text-right">${{ number_format($product->stock * $product->purchase_price, 2) }}</td>
                                    <td>
                                        @if($product->stock == 0)
                                            <span class="badge bg-danger">Sin Stock</span>
                                        @elseif($product->isLowStock())
                                            <span class="badge bg-warning">Bajo Stock</span>
                                        @else
                                            <span class="badge bg-success">En Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                                        <p>No hay productos disponibles según los criterios seleccionados</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="6">TOTAL</td>
                                <td class="text-center">{{ $totalStock }}</td>
                                <td class="text-right">${{ number_format($inventoryValue, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @elseif(request('report_type') == 'stock_movement')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Referencia</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockMovements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $movement->product->name ?? 'Producto eliminado' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $movement->type == 'add' ? 'success' : 'danger' }}">
                                            {{ $movement->type == 'add' ? 'Entrada' : 'Salida' }}
                                        </span>
                                    </td>
                                    <td class="text-{{ $movement->type == 'add' ? 'success' : 'danger' }}">
                                        {{ $movement->type == 'add' ? '+' : '-' }}{{ $movement->quantity }}
                                    </td>
                                    <td>{{ $movement->reference }}</td>
                                    <td>{{ $movement->user->name ?? 'Sistema' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                                        <p>No hay movimientos de stock registrados en el período seleccionado</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
            
            <!-- Paginación -->
            @if(isset($products) && $products->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @elseif(isset($stockMovements) && $stockMovements->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $stockMovements->appends(request()->query())->links() }}
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
        // Mostrar/ocultar campos según el tipo de reporte
        const reportTypeSelect = document.getElementById('report_type');
        const dateRangeFields = document.querySelectorAll('.date-range');
        
        reportTypeSelect.addEventListener('change', function() {
            if (this.value === 'stock_movement') {
                dateRangeFields.forEach(field => field.classList.remove('d-none'));
            } else {
                dateRangeFields.forEach(field => field.classList.add('d-none'));
            }
        });
        
        // Configuración del gráfico de distribución por categoría
        const categoryData = @json($categoryChartData);
        const ctxPie = document.getElementById('categoryPieChart').getContext('2d');
        const categoryPieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: categoryData.labels,
                datasets: [{
                    data: categoryData.data,
                    backgroundColor: categoryData.colors,
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
                                label += context.raw + ' productos (' + 
                                        ((context.raw / categoryData.data.reduce((a, b) => a + b, 0)) * 100).toFixed(1) + '%)';
                                return label;
                            }
                        }
                    }
                }
            },
        });
        
        // Configuración del gráfico de valor de productos
        const productValueData = @json($productValueChartData);
        const ctxBar = document.getElementById('productValueChart').getContext('2d');
        const productValueChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: productValueData.labels,
                datasets: [{
                    label: 'Valor del Inventario',
                    backgroundColor: 'rgba(78, 115, 223, 0.7)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1,
                    data: productValueData.data,
                }],
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
