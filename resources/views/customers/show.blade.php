@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalles del Cliente</h1>
        <div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-1"></i> Editar
                </a>
            @endif
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información del Cliente -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Información Personal</h6>
                    <span class="badge bg-{{ $customer->status ? 'success' : 'danger' }}">
                        {{ $customer->status ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fas fa-user fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $customer->name }}</h4>
                                <p class="text-muted mb-0">Cliente desde {{ $customer->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Datos de Contacto</h6>
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted"><i class="fas fa-envelope mr-2"></i> Email:</span>
                                    <span>{{ $customer->email ?: 'No registrado' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted"><i class="fas fa-phone mr-2"></i> Teléfono:</span>
                                    <span>{{ $customer->phone ?: 'No registrado' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted"><i class="fas fa-id-card mr-2"></i> Documento:</span>
                                    <span>{{ $customer->document_type }}: {{ $customer->document_number }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Dirección</h6>
                            <address class="mb-4">
                                {{ $customer->address ?: 'Dirección no registrada' }}<br>
                                @if($customer->city || $customer->postal_code)
                                    {{ $customer->city ?: '' }} {{ $customer->postal_code ? ', '.$customer->postal_code : '' }}
                                @endif
                            </address>
                            
                            <h6 class="font-weight-bold">Información Financiera</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">Balance:</span>
                                    <span class="{{ $customer->balance < 0 ? 'text-danger' : 'text-success' }}">
                                        ${{ number_format($customer->balance, 2) }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">Total Compras:</span>
                                    <span>${{ number_format($totalPurchases, 2) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    @if($customer->notes)
                        <div class="mt-4">
                            <h6 class="font-weight-bold">Notas</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $customer->notes }}
                            </div>
                        </div>
                    @endif
                    
                    @if($customer->balance < 0)
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                                <i class="fas fa-dollar-sign mr-1"></i> Registrar Pago
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Ventas Recientes y Transacciones -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ventas Recientes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Factura</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                    <tr>
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->created_at->format('d/m/Y') }}</td>
                                        <td>${{ number_format($sale->total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $sale->status == 'completed' ? 'success' : ($sale->status == 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($sale->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3">No hay ventas registradas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('sales.index', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-primary">
                            Ver todas las ventas
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Historial de Pagos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Referencia</th>
                                    <th>Método</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentHistory as $payment)
                                    <tr>
                                        <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $payment->reference }}</td>
                                        <td>{{ ucfirst($payment->payment_method) }}</td>
                                        <td class="text-success">${{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3">No hay pagos registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Pago -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- TODO: Implementar ruta y método para pagos de clientes --}}
            <form action="{{ route('customers.index') }}" method="GET">
                {{-- Este formulario es un placeholder. La funcionalidad de pagos aún no está implementada --}}
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        Balance actual: <strong>${{ number_format(abs($customer->balance), 2) }}</strong> a pagar
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Monto <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" max="{{ abs($customer->balance) }}"
                                   class="form-control" id="amount" name="amount" 
                                   value="{{ abs($customer->balance) }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Método de Pago <span class="text-danger">*</span></label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reference" class="form-label">Referencia</label>
                        <input type="text" class="form-control" id="reference" name="reference" 
                               placeholder="Ej: Recibo #123, Transacción #456">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="alert('La funcionalidad de pagos aún no está implementada.')">
                        <i class="fas fa-save mr-1"></i> Registrar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
