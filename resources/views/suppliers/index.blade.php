@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Proveedores</h1>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Nuevo Proveedor
        </a>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('suppliers.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Nombre, email o número fiscal" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-5 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Restablecer
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Proveedores -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Proveedores</h6>
            <span>Total: {{ $suppliers->total() }} proveedores</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Número Fiscal</th>
                            <th>Productos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->contact_person }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->tax_number }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $supplier->products_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $supplier->status ? 'success' : 'danger' }}">
                                        {{ $supplier->status ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal" 
                                                data-supplier-id="{{ $supplier->id }}"
                                                data-supplier-name="{{ $supplier->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                    <p>No hay proveedores disponibles</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $suppliers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para Eliminar Proveedor -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar al proveedor <strong id="deleteSupplierName"></strong>?</p>
                <div id="supplierHasProducts" class="alert alert-warning" style="display: none;">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Este proveedor tiene productos asociados. Si lo elimina, los productos quedarán sin proveedor.
                </div>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
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
        // Eliminar proveedor
        const deleteBtns = document.querySelectorAll('.delete-btn');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const supplierId = this.getAttribute('data-supplier-id');
                const supplierName = this.getAttribute('data-supplier-name');
                const productsCount = this.closest('tr').querySelector('.badge').textContent;
                
                document.getElementById('deleteSupplierName').textContent = supplierName;
                document.getElementById('deleteForm').action = `/suppliers/${supplierId}`;
                
                // Mostrar advertencia si el proveedor tiene productos
                if (parseInt(productsCount) > 0) {
                    document.getElementById('supplierHasProducts').style.display = 'block';
                } else {
                    document.getElementById('supplierHasProducts').style.display = 'none';
                }
            });
        });
    });
</script>
@endpush
