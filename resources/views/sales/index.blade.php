@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Historial de Ventas</h1>
        <a href="{{ route('pos') }}" class="btn btn-primary">
            <i class="fas fa-cash-register mr-1"></i> Nueva Venta
        </a>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('sales.index') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label for="invoice" class="form-label">Nº Factura</label>
                    <input type="text" class="form-control" id="invoice" name="invoice" value="{{ request('invoice') }}">
                </div>
                <div class="col-md-3">
                    <label for="customer_id" class="form-label">Cliente</label>
                    <select class="form-select" id="customer_id" name="customer_id">
                        <option value="">Todos los clientes</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completado</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="from_date" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label for="to_date" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen de Ventas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Ventas (Filtrado)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($totalSales, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                                Ventas Completadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $completedSales }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Ventas Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pendingSales }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Ventas Canceladas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $cancelledSales }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Ventas</h6>
            <div>
                <a href="{{ route('sales.index', array_merge(request()->query(), ['export' => true])) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                </a>
                <a href="{{ route('sales.index', array_merge(request()->query(), ['print' => true])) }}" class="btn btn-sm btn-secondary" target="_blank">
                    <i class="fas fa-print mr-1"></i> Imprimir
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nº Factura</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Items</th>
                            <th>Subtotal</th>
                            <th>Impuesto</th>
                            <th>Descuento</th>
                            <th>Total</th>
                            <th>Método Pago</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->invoice_number }}</td>
                                <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $sale->customer->name ?? 'Cliente Casual' }}</td>
                                <td class="text-center">{{ $sale->details_count }}</td>
                                <td class="text-end">${{ number_format($sale->subtotal, 2) }}</td>
                                <td class="text-end">${{ number_format($sale->tax, 2) }}</td>
                                <td class="text-end">${{ number_format($sale->discount, 2) }}</td>
                                <td class="text-end font-weight-bold">${{ number_format($sale->total, 2) }}</td>
                                <td>{{ ucfirst($sale->payment_method) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $sale->status == 'completed' ? 'success' : ($sale->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-secondary" onclick="alert('La función de impresión estará disponible próximamente')">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @if($sale->status == 'pending' && Auth::user()->isAdmin())
                                            <a href="{{ route('sales.edit', $sale) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($sale->status != 'canceled' && Auth::user()->isAdmin())
                                            <button type="button" class="btn btn-sm btn-danger cancel-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#cancelSaleModal" 
                                                    data-sale-id="{{ $sale->id }}"
                                                    data-sale-invoice="{{ $sale->invoice_number }}">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                    <p>No hay ventas registradas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $sales->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para Cancelar Venta -->
<div class="modal fade" id="cancelSaleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Cancelación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cancelSaleForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>¿Está seguro de que desea cancelar la venta <strong id="cancelSaleInvoice"></strong>?</p>
                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label">Motivo de Cancelación</label>
                        <textarea class="form-control" id="cancel_reason" name="cancel_reason" rows="3" required></textarea>
                    </div>
                    <p class="text-danger">Esta acción no se puede deshacer y afectará el inventario y los reportes financieros.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Cancelar Venta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar modal para cancelar venta
        const cancelBtns = document.querySelectorAll('.cancel-btn');
        cancelBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const saleId = this.getAttribute('data-sale-id');
                const saleInvoice = this.getAttribute('data-sale-invoice');
                
                document.getElementById('cancelSaleInvoice').textContent = saleInvoice;
                document.getElementById('cancelSaleForm').action = `/sales/${saleId}/cancel`;
            });
        });
    });
</script>
@endpush
