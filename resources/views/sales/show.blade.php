@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalle de Venta</h1>
        <div>
            {{-- Comentado temporalmente hasta que se implemente la ruta de impresión --}}
            <a href="#" class="btn btn-secondary" onclick="alert('La función de impresión estará disponible próximamente')">
                <i class="fas fa-print mr-1"></i> Imprimir
            </a>
            <a href="{{ route('sales.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información de la Venta -->
        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Información de la Venta</h6>
                    <span class="badge bg-{{ isset($sale->status) && $sale->status == 'completed' ? 'success' : (isset($sale->status) && $sale->status == 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($sale->status ?? 'unknown') }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4 p-3 bg-light rounded">
                        <div class="row mb-2">
                            <div class="col-6">
                                <span class="text-muted">Factura Nº:</span>
                                <h5 class="font-weight-bold">{{ $sale->invoice_number }}</h5>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-muted">Fecha:</span>
                                <div>{{ $sale->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <span class="text-muted">Vendedor:</span>
                                <div>{{ isset($sale->user) ? $sale->user->name : 'No registrado' }}</div>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Método de Pago:</span>
                                <div>{{ ucfirst($sale->payment_method) }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="font-weight-bold mb-3">Cliente</h6>
                    <div class="mb-4">
                        @if($sale->customer)
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $sale->customer->name }}</h6>
                                    <small class="text-muted">{{ $sale->customer->document_type }}: {{ $sale->customer->document_number }}</small>
                                </div>
                                <a href="{{ route('customers.show', $sale->customer) }}" class="btn btn-sm btn-outline-primary ms-auto">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <span class="text-muted">Teléfono:</span>
                                    <div>{{ $sale->customer->phone ?: 'No registrado' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted">Email:</span>
                                    <div>{{ $sale->customer->email ?: 'No registrado' }}</div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i> Cliente casual (sin registro)
                            </div>
                        @endif
                    </div>
                    
                    @if($sale->status == 'canceled')
                        <div class="alert alert-danger">
                            <h6 class="font-weight-bold">Venta Cancelada</h6>
                            <p class="mb-0"><strong>Motivo:</strong> {{ $sale->cancel_reason ?: 'No se especificó motivo' }}</p>
                            <small>Cancelada el {{ $sale->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between mt-4">
                        @if($sale->status == 'pending')
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-warning">
                                    <i class="fas fa-edit mr-1"></i> Editar Venta
                                </a>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completePaymentModal">
                                    <i class="fas fa-check-circle mr-1"></i> Completar Pago
                                </button>
                            @endif
                        @elseif($sale->status == 'completed')
                            <a href="{{ route('sales.print-invoice', $sale) }}" class="btn btn-secondary" target="_blank">
                                <i class="fas fa-print mr-1"></i> Imprimir Recibo
                            </a>
                            @if(Auth::user()->isAdmin())
                                {{-- Funcionalidad de devolución pendiente de implementar --}}
                                <a href="#" class="btn btn-warning" onclick="alert('La función de devoluciones estará disponible próximamente')">
                                    <i class="fas fa-undo mr-1"></i> Devolución
                                </a>
                                {{-- Funcionalidad de envío de email pendiente de implementar --}}
                                <button type="button" class="btn btn-info" onclick="alert('La función de envío por email estará disponible próximamente')">
                                    <i class="fas fa-envelope mr-1"></i> Enviar por Email
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Totales -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Resumen</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Subtotal:</td>
                            <td class="text-end">${{ number_format($sale->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Impuesto ({{ $sale->tax_rate }}%):</td>
                            <td class="text-end">${{ number_format($sale->tax, 2) }}</td>
                        </tr>
                        @if($sale->discount > 0)
                        <tr>
                            <td class="text-muted">Descuento:</td>
                            <td class="text-end">-${{ number_format($sale->discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td class="font-weight-bold">TOTAL:</td>
                            <td class="text-end font-weight-bold h4">${{ number_format($sale->total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Detalles de la Venta -->
        <div class="col-md-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Productos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" width="50">#</th>
                                    <th scope="col">Producto</th>
                                    <th scope="col" class="text-center">Cantidad</th>
                                    <th scope="col" class="text-end">Precio Unit.</th>
                                    <th scope="col" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sale->saleDetails ?? [] as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if(isset($detail->product) && isset($detail->product->image))
                                                    <img src="{{ asset('storage/' . $detail->product->image) }}" 
                                                         alt="{{ $detail->product->name }}" 
                                                         class="img-thumbnail me-2" style="width: 40px; height: 40px; object-fit: contain;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-box text-secondary"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ isset($detail->product) ? $detail->product->name : 'Producto no disponible' }}</h6>
                                                    <small class="text-muted">{{ isset($detail->product) ? $detail->product->code : 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $detail->quantity ?? 0 }}</td>
                                        <td class="text-end">${{ number_format($detail->price ?? 0, 2) }}</td>
                                        <td class="text-end">${{ number_format($detail->total ?? ($detail->quantity * $detail->price ?? 0), 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3">
                                            <div class="alert alert-info mb-0">
                                                No hay detalles disponibles para esta venta.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Historial de Transacciones -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Historial de Transacciones</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Referencia</th>
                                    <th>Monto</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->type == 'income' ? 'success' : 'danger' }}">
                                                {{ $transaction->type == 'income' ? 'Ingreso' : 'Egreso' }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction->reference }}</td>
                                        <td class="text-end">${{ number_format($transaction->amount, 2) }}</td>
                                        <td>{{ $transaction->user->name ?? 'Sistema' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3">No hay transacciones registradas</td>
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

<!-- Modal para Completar Pago -->
<div class="modal fade" id="completePaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Completar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sales.complete', $sale) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        Total a pagar: <strong>${{ number_format($sale->total, 2) }}</strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Método de Pago <span class="text-danger">*</span></label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="cash" {{ $sale->payment_method == 'cash' ? 'selected' : '' }}>Efectivo</option>
                            <option value="card" {{ $sale->payment_method == 'card' ? 'selected' : '' }}>Tarjeta</option>
                            <option value="transfer" {{ $sale->payment_method == 'transfer' ? 'selected' : '' }}>Transferencia</option>
                            <option value="credit" {{ $sale->payment_method == 'credit' ? 'selected' : '' }}>Crédito</option>
                        </select>
                    </div>
                    
                    <div id="cashFields" class="mb-3">
                        <label for="amount_tendered" class="form-label">Monto Entregado</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="{{ $sale->total }}" 
                                   class="form-control" id="amount_tendered" name="amount_tendered" 
                                   value="{{ $sale->total }}">
                        </div>
                        <div class="form-text">El cambio se calculará automáticamente</div>
                    </div>
                    
                    <div id="cardFields" class="mb-3" style="display: none;">
                        <label for="card_reference" class="form-label">Referencia de Tarjeta</label>
                        <input type="text" class="form-control" id="card_reference" name="card_reference">
                    </div>
                    
                    <div id="transferFields" class="mb-3" style="display: none;">
                        <label for="transfer_reference" class="form-label">Referencia de Transferencia</label>
                        <input type="text" class="form-control" id="transfer_reference" name="transfer_reference">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas Adicionales</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle mr-1"></i> Completar Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar/ocultar campos según método de pago
        const paymentMethodSelect = document.getElementById('payment_method');
        const cashFields = document.getElementById('cashFields');
        const cardFields = document.getElementById('cardFields');
        const transferFields = document.getElementById('transferFields');
        
        function updateFields() {
            const method = paymentMethodSelect.value;
            
            cashFields.style.display = method === 'cash' ? 'block' : 'none';
            cardFields.style.display = method === 'card' ? 'block' : 'none';
            transferFields.style.display = method === 'transfer' ? 'block' : 'none';
        }
        
        paymentMethodSelect.addEventListener('change', updateFields);
        updateFields(); // Inicializar
        
        // Calcular cambio en tiempo real
        const amountTendered = document.getElementById('amount_tendered');
        const totalAmount = {{ $sale->total }};
        
        amountTendered.addEventListener('input', function() {
            const tendered = parseFloat(this.value) || 0;
            const change = tendered - totalAmount;
            
            const changeText = document.querySelector('.form-text');
            if (change >= 0) {
                changeText.textContent = `Cambio a devolver: $${change.toFixed(2)}`;
            } else {
                changeText.textContent = `Falta: $${Math.abs(change).toFixed(2)}`;
            }
        });
    });
</script>
@endpush
