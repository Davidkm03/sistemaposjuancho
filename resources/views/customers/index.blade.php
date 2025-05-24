@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Clientes</h1>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus mr-1"></i> Nuevo Cliente
        </a>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('customers.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Nombre, email o documento" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="document_type" class="form-label">Tipo de Documento</label>
                    <select class="form-select" id="document_type" name="document_type">
                        <option value="">Todos</option>
                        <option value="DNI" {{ request('document_type') == 'DNI' ? 'selected' : '' }}>DNI</option>
                        <option value="RUC" {{ request('document_type') == 'RUC' ? 'selected' : '' }}>RUC</option>
                        <option value="PASSPORT" {{ request('document_type') == 'PASSPORT' ? 'selected' : '' }}>Pasaporte</option>
                        <option value="OTHER" {{ request('document_type') == 'OTHER' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Restablecer
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Clientes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Clientes</h6>
            <span>Total: {{ $customers->total() }} clientes</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Documento</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Balance</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->document_type }}: {{ $customer->document_number }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td class="text-end {{ $customer->balance < 0 ? 'text-danger' : '' }}">
                                    ${{ number_format($customer->balance, 2) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $customer->status ? 'success' : 'danger' }}">
                                        {{ $customer->status ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $customer->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p>No hay clientes disponibles</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $customers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para Eliminar Cliente -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar al cliente <strong id="deleteCustomerName"></strong>?</p>
                <p class="text-danger">Esta acción no se puede deshacer. Si el cliente tiene ventas asociadas, se recomienda desactivarlo en lugar de eliminarlo.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" action="" method="POST">
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
        // Eliminar cliente
        const deleteBtns = document.querySelectorAll('.delete-btn');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const customerId = this.getAttribute('data-customer-id');
                const customerName = this.getAttribute('data-customer-name');
                
                document.getElementById('deleteCustomerName').textContent = customerName;
                document.getElementById('deleteForm').action = `/customers/${customerId}`;
            });
        });
    });
</script>
@endpush
