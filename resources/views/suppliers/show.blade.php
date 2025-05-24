@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalles del Proveedor</h1>
        <div>
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> Editar
            </a>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información del Proveedor -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Información General</h6>
                    <span class="badge bg-{{ $supplier->status ? 'success' : 'danger' }}">
                        {{ $supplier->status ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fas fa-building fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $supplier->name }}</h4>
                                <p class="text-muted mb-0">Proveedor desde {{ $supplier->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Datos Básicos</h6>
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted"><i class="fas fa-id-card mr-2"></i> Número Fiscal:</span>
                                    <span>{{ $supplier->tax_number }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted"><i class="fas fa-user mr-2"></i> Contacto:</span>
                                    <span>{{ $supplier->contact_person ?: 'No registrado' }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Datos de Contacto</h6>
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted"><i class="fas fa-envelope mr-2"></i> Email:</span>
                                    <span>{{ $supplier->email }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted"><i class="fas fa-phone mr-2"></i> Teléfono:</span>
                                    <span>{{ $supplier->phone }}</span>
                                </li>
                                @if($supplier->website)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted"><i class="fas fa-globe mr-2"></i> Sitio Web:</span>
                                    <a href="{{ $supplier->website }}" target="_blank">{{ $supplier->website }}</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    
                    <h6 class="font-weight-bold">Dirección</h6>
                    <address class="mb-4">
                        {{ $supplier->address ?: 'Dirección no registrada' }}<br>
                        @if($supplier->city || $supplier->state || $supplier->postal_code)
                            {{ $supplier->city ?: '' }}{{ $supplier->city && $supplier->state ? ', ' : '' }}
                            {{ $supplier->state ?: '' }}{{ ($supplier->city || $supplier->state) && $supplier->postal_code ? ', ' : '' }}
                            {{ $supplier->postal_code ?: '' }}
                        @endif
                    </address>
                    
                    @if($supplier->notes)
                        <div class="mt-4">
                            <h6 class="font-weight-bold">Notas</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $supplier->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Productos del Proveedor -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Productos Suministrados</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Precio Compra</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>{{ $product->code }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>${{ number_format($product->purchase_price, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $product->isLowStock() ? 'warning' : 'success' }}">
                                                {{ $product->stock }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3">No hay productos asociados a este proveedor</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    @if($products->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $products->links() }}
                        </div>
                    @endif
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('products.create', ['supplier_id' => $supplier->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus mr-1"></i> Agregar Producto
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas de Compras -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas de Compras</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h5 class="text-primary">{{ $totalProducts }}</h5>
                            <small class="text-muted">Productos</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h5 class="text-success">${{ number_format($totalPurchases, 2) }}</h5>
                            <small class="text-muted">Compras Totales</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="font-weight-bold mb-3">Compras Recientes</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Productos</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPurchases as $purchase)
                                    <tr>
                                        <td>{{ $purchase->date->format('d/m/Y') }}</td>
                                        <td>{{ $purchase->items_count }}</td>
                                        <td>${{ number_format($purchase->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3">No hay compras recientes</td>
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
@endsection
