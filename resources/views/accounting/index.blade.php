@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Transacciones Contables</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTransactionModal">
            <i class="fas fa-plus mr-1"></i> Nueva Transacción
        </button>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('accounting.index') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label for="reference" class="form-label">Referencia</label>
                    <input type="text" class="form-control" id="reference" name="reference" value="{{ request('reference') }}">
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">Tipo</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Todos</option>
                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Ingresos</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Gastos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="category" class="form-label">Categoría</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Todas</option>
                        <option value="sale" {{ request('category') == 'sale' ? 'selected' : '' }}>Ventas</option>
                        <option value="purchase" {{ request('category') == 'purchase' ? 'selected' : '' }}>Compras</option>
                        <option value="salary" {{ request('category') == 'salary' ? 'selected' : '' }}>Salarios</option>
                        <option value="rent" {{ request('category') == 'rent' ? 'selected' : '' }}>Alquiler</option>
                        <option value="utility" {{ request('category') == 'utility' ? 'selected' : '' }}>Servicios</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Otros</option>
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
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('accounting.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
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
                                Ingresos (Filtrado)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($totals['income'], 2) }}
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
                                Gastos (Filtrado)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format(abs($totals['expense']), 2) }}
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
                                Balance Neto (Filtrado)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($totals['balance'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
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
                                Transacciones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $transactions->total() }}
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

    <!-- Tabla de Transacciones -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Transacciones</h6>
            <div>
                {{-- Botones de exportación y impresión (deshabilitados temporalmente) --}}
                {{-- 
                <a href="{{ route('accounting.export', request()->query()) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                </a>
                <a href="{{ route('accounting.print', request()->query()) }}" class="btn btn-sm btn-secondary" target="_blank">
                    <i class="fas fa-print mr-1"></i> Imprimir
                </a>
                --}}
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Referencia</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td>{{ $transaction->reference }}</td>
                                <td>{{ ucfirst($transaction->category) }}</td>
                                <td>{{ Str::limit($transaction->description, 50) }}</td>
                                <td>
                                    <span class="badge bg-{{ $transaction->type == 'income' ? 'success' : 'danger' }}">
                                        {{ $transaction->type == 'income' ? 'Ingreso' : 'Gasto' }}
                                    </span>
                                </td>
                                <td class="text-end font-weight-bold">
                                    ${{ number_format($transaction->amount, 2) }}
                                </td>
                                <td>{{ $transaction->user->name ?? 'Sistema' }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-info view-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewTransactionModal" 
                                                data-transaction-id="{{ $transaction->id }}"
                                                data-transaction-date="{{ $transaction->transaction_date->format('Y-m-d') }}"
                                                data-transaction-reference="{{ $transaction->reference }}"
                                                data-transaction-category="{{ $transaction->category }}"
                                                data-transaction-type="{{ $transaction->type }}"
                                                data-transaction-amount="{{ $transaction->amount }}"
                                                data-transaction-description="{{ $transaction->description }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(!$transaction->sale_id) <!-- Solo editar si no está vinculada a una venta -->
                                            <button type="button" class="btn btn-sm btn-primary edit-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editTransactionModal" 
                                                    data-transaction-id="{{ $transaction->id }}"
                                                    data-transaction-date="{{ $transaction->transaction_date->format('Y-m-d') }}"
                                                    data-transaction-reference="{{ $transaction->reference }}"
                                                    data-transaction-category="{{ $transaction->category }}"
                                                    data-transaction-type="{{ $transaction->type }}"
                                                    data-transaction-amount="{{ $transaction->amount }}"
                                                    data-transaction-description="{{ $transaction->description }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteTransactionModal" 
                                                    data-transaction-id="{{ $transaction->id }}"
                                                    data-transaction-reference="{{ $transaction->reference }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                                    <p>No hay transacciones registradas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Transacción -->
<div class="modal fade" id="viewTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Transacción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Fecha:</th>
                        <td id="viewDate"></td>
                    </tr>
                    <tr>
                        <th>Referencia:</th>
                        <td id="viewReference"></td>
                    </tr>
                    <tr>
                        <th>Tipo:</th>
                        <td id="viewType"></td>
                    </tr>
                    <tr>
                        <th>Categoría:</th>
                        <td id="viewCategory"></td>
                    </tr>
                    <tr>
                        <th>Monto:</th>
                        <td id="viewAmount" class="font-weight-bold"></td>
                    </tr>
                    <tr>
                        <th>Descripción:</th>
                        <td id="viewDescription"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear Transacción -->
<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Transacción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accounting.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="income">Ingreso</option>
                            <option value="expense">Gasto</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="sale">Venta</option>
                            <option value="purchase">Compra</option>
                            <option value="salary">Salario</option>
                            <option value="rent">Alquiler</option>
                            <option value="utility">Servicios</option>
                            <option value="other">Otro</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Monto <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" 
                                   class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount') }}" required>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="transaction_date" class="form-label">Fecha <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" 
                               id="transaction_date" name="transaction_date" 
                               value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                        @error('transaction_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="reference" class="form-label">Referencia</label>
                        <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                               id="reference" name="reference" value="{{ old('reference') }}">
                        @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Transacción -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Transacción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTransactionForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_type" name="type" required>
                            <option value="income">Ingreso</option>
                            <option value="expense">Gasto</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_category" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_category" name="category" required>
                            <option value="sale">Venta</option>
                            <option value="purchase">Compra</option>
                            <option value="salary">Salario</option>
                            <option value="rent">Alquiler</option>
                            <option value="utility">Servicios</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_amount" class="form-label">Monto <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" 
                                   class="form-control" id="edit_amount" name="amount" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_transaction_date" class="form-label">Fecha <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="edit_transaction_date" name="transaction_date" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_reference" class="form-label">Referencia</label>
                        <input type="text" class="form-control" id="edit_reference" name="reference">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Eliminar Transacción -->
<div class="modal fade" id="deleteTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar la transacción <strong id="deleteTransactionReference"></strong>?</p>
                <p class="text-danger">Esta acción no se puede deshacer y podría afectar los balances contables.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteTransactionForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ver transacción
        const viewBtns = document.querySelectorAll('.view-btn');
        viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const type = this.getAttribute('data-transaction-type');
                const amount = parseFloat(this.getAttribute('data-transaction-amount'));
                const formattedAmount = new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'USD' }).format(amount);
                
                document.getElementById('viewDate').textContent = this.getAttribute('data-transaction-date');
                document.getElementById('viewReference').textContent = this.getAttribute('data-transaction-reference');
                document.getElementById('viewType').innerHTML = `<span class="badge bg-${type === 'income' ? 'success' : 'danger'}">${type === 'income' ? 'Ingreso' : 'Gasto'}</span>`;
                document.getElementById('viewCategory').textContent = this.getAttribute('data-transaction-category');
                document.getElementById('viewAmount').textContent = formattedAmount;
                document.getElementById('viewDescription').textContent = this.getAttribute('data-transaction-description');
            });
        });
        
        // Editar transacción
        const editBtns = document.querySelectorAll('.edit-btn');
        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const transactionId = this.getAttribute('data-transaction-id');
                
                document.getElementById('edit_type').value = this.getAttribute('data-transaction-type');
                document.getElementById('edit_category').value = this.getAttribute('data-transaction-category');
                document.getElementById('edit_amount').value = this.getAttribute('data-transaction-amount');
                document.getElementById('edit_transaction_date').value = this.getAttribute('data-transaction-date');
                document.getElementById('edit_reference').value = this.getAttribute('data-transaction-reference');
                document.getElementById('edit_description').value = this.getAttribute('data-transaction-description');
                
                document.getElementById('editTransactionForm').action = `/accounting/${transactionId}`;
            });
        });
        
        // Eliminar transacción
        const deleteBtns = document.querySelectorAll('.delete-btn');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const transactionId = this.getAttribute('data-transaction-id');
                const transactionReference = this.getAttribute('data-transaction-reference') || 'sin referencia';
                
                document.getElementById('deleteTransactionReference').textContent = transactionReference;
                document.getElementById('deleteTransactionForm').action = `/accounting/${transactionId}`;
            });
        });
    });
</script>
@endpush
