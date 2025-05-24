@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Editar Producto</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    <!-- Edit Product Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información del Producto</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <!-- Información básica -->
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Datos Básicos</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="code" class="form-label">Código <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                                   id="code" name="code" value="{{ old('code', $product->code) }}" required>
                                            <button type="button" class="btn btn-outline-secondary" id="generateCodeBtn">
                                                <i class="fas fa-barcode"></i>
                                            </button>
                                        </div>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Descripción</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="category_id" class="form-label">Categoría</label>
                                        <select class="form-select @error('category_id') is-invalid @enderror" 
                                                id="category_id" name="category_id">
                                            <option value="">Seleccionar categoría</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="supplier_id" class="form-label">Proveedor</label>
                                        <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                                id="supplier_id" name="supplier_id">
                                            <option value="">Seleccionar proveedor</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Precios y Stock</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="purchase_price" class="form-label">Precio de Compra <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" min="0" 
                                                   class="form-control @error('purchase_price') is-invalid @enderror" 
                                                   id="purchase_price" name="purchase_price" 
                                                   value="{{ old('purchase_price', $product->purchase_price) }}" required>
                                        </div>
                                        @error('purchase_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="selling_price" class="form-label">Precio de Venta <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" min="0" 
                                                   class="form-control @error('selling_price') is-invalid @enderror" 
                                                   id="selling_price" name="selling_price" 
                                                   value="{{ old('selling_price', $product->selling_price) }}" required>
                                        </div>
                                        @error('selling_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
                                        <input type="number" min="0" 
                                               class="form-control @error('stock') is-invalid @enderror" 
                                               id="stock" name="stock" 
                                               value="{{ old('stock', $product->stock) }}" required readonly>
                                        <small class="form-text text-muted">Utilice la opción "Actualizar Stock" para modificar el inventario</small>
                                        @error('stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="min_stock" class="form-label">Stock Mínimo</label>
                                        <input type="number" min="0" 
                                               class="form-control @error('min_stock') is-invalid @enderror" 
                                               id="min_stock" name="min_stock" 
                                               value="{{ old('min_stock', $product->min_stock) }}">
                                        @error('min_stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tax_rate" class="form-label">Tasa de Impuesto (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" 
                                               class="form-control @error('tax_rate') is-invalid @enderror" 
                                               id="tax_rate" name="tax_rate" 
                                               value="{{ old('tax_rate', $product->tax_rate) }}">
                                        @error('tax_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1" 
                                           {{ old('status', $product->status) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">Activo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Imagen del producto -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Imagen del Producto</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img id="imagePreview" 
                                         src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}" 
                                         class="img-fluid img-thumbnail" alt="Vista previa" 
                                         style="max-height: 250px; width: auto;">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Cambiar imagen</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Formatos permitidos: JPG, PNG. Máx 2MB</div>
                                </div>
                                
                                <button type="button" class="btn btn-outline-danger btn-sm" id="removeImageBtn">
                                    <i class="fas fa-times"></i> Quitar imagen
                                </button>
                                
                                <input type="hidden" name="remove_image" id="remove_image" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Actualizar Producto
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
        // Generar código aleatorio
        document.getElementById('generateCodeBtn').addEventListener('click', function() {
            const randomCode = 'P' + Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
            document.getElementById('code').value = randomCode;
        });
        
        // Previsualizar imagen
        document.getElementById('image').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('remove_image').value = '0';
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Quitar imagen
        document.getElementById('removeImageBtn').addEventListener('click', function() {
            document.getElementById('image').value = '';
            document.getElementById('imagePreview').src = '{{ asset("images/no-image.png") }}';
            document.getElementById('remove_image').value = '1';
        });
    });
</script>
@endpush
