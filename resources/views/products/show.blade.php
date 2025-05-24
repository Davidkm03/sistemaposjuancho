@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalles del Producto</h1>
        <div>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> Editar
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información del Producto -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Información General</h6>
                    <span class="badge bg-{{ $product->status ? 'success' : 'danger' }}">
                        {{ $product->status ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th class="text-muted">Código:</th>
                                    <td>{{ $product->code }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Nombre:</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Categoría:</th>
                                    <td>{{ $product->category->name ?? 'Sin categoría' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Proveedor:</th>
                                    <td>{{ $product->supplier->name ?? 'Sin proveedor' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th class="text-muted">Precio de Compra:</th>
                                    <td>${{ number_format($product->purchase_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Precio de Venta:</th>
                                    <td>${{ number_format($product->selling_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Tasa de Impuesto:</th>
                                    <td>{{ $product->tax_rate }}%</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Margen de Ganancia:</th>
                                    <td>
                                        @php
                                            $profit = $product->selling_price - $product->purchase_price;
                                            $margin = $product->purchase_price > 0 ? ($profit / $product->purchase_price) * 100 : 0;
                                        @endphp
                                        {{ number_format($margin, 2) }}% (${{ number_format($profit, 2) }})
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="font-weight-bold">Descripción:</h6>
                    <p>{{ $product->description ?: 'Sin descripción' }}</p>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body text-center">
                                    <h5 class="mb-0">{{ $product->stock }}</h5>
                                    <small>Stock Actual</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body text-center">
                                    <h5 class="mb-0">{{ $product->min_stock }}</h5>
                                    <small>Stock Mínimo</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ $product->isLowStock() ? 'bg-danger' : 'bg-success' }} text-white mb-4">
                                <div class="card-body text-center">
                                    <h5 class="mb-0">{{ $product->stock - $product->min_stock }}</h5>
                                    <small>Disponible</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStockModal">
                            <i class="fas fa-sync-alt mr-1"></i> Actualizar Stock
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Historial de Stock -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Historial de Stock</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Referencia</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockHistory as $history)
                                    <tr>
                                        <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $history->type == 'add' ? 'success' : 'danger' }}">
                                                {{ $history->type == 'add' ? 'Entrada' : 'Salida' }}
                                            </span>
                                        </td>
                                        <td class="text-{{ $history->type == 'add' ? 'success' : 'danger' }}">
                                            {{ $history->type == 'add' ? '+' : '-' }}{{ $history->quantity }}
                                        </td>
                                        <td>{{ $history->reference }}</td>
                                        <td>{{ $history->user->name ?? 'Sistema' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3">No hay registros de movimientos de stock</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($stockHistory->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $stockHistory->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Imagen y Ventas -->
        <div class="col-md-4">
            <!-- Imagen del Producto -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Imagen</h6>
                </div>
                <div class="card-body text-center">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid img-thumbnail" style="max-height: 300px;">
                    @else
                        <div class="py-5">
                            <i class="fas fa-box fa-6x text-secondary mb-3"></i>
                            <p class="text-muted">No hay imagen disponible</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Estadísticas de Ventas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas de Ventas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center mb-3">
                            <h5 class="text-primary">{{ $totalSold }}</h5>
                            <small class="text-muted">Unidades Vendidas</small>
                        </div>
                        <div class="col-6 text-center mb-3">
                            <h5 class="text-success">${{ number_format($totalRevenue, 2) }}</h5>
                            <small class="text-muted">Ingresos Totales</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="font-weight-bold mb-3">Ventas Recientes</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                    <tr>
                                        <td>{{ $sale->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $sale->quantity }}</td>
                                        <td>${{ number_format($sale->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3">No hay ventas recientes</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('sales.index', ['product_id' => $product->id]) }}" class="btn btn-sm btn-outline-primary">
                            Ver todas las ventas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Actualizar Stock -->
<div class="modal fade" id="updateStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('products.update-stock', $product) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <h6 class="mb-3 text-primary">{{ $product->name }}</h6>
                    <p>Stock actual: <strong>{{ $product->stock }}</strong> unidades</p>
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="operation" class="form-label">Operación</label>
                        <select class="form-select" id="operation" name="operation" required>
                            <option value="add">Agregar al stock</option>
                            <option value="subtract">Restar del stock</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reference" class="form-label">Referencia</label>
                        <input type="text" class="form-control" id="reference" name="reference" 
                               placeholder="Ej: Compra #123, Ajuste de inventario">
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
@endsection
