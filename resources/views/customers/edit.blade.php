@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Editar Cliente</h1>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    <!-- Edit Customer Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información del Cliente</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('customers.update', $customer) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Información personal -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Datos Personales</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="document_type" class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                                        <select class="form-select @error('document_type') is-invalid @enderror" 
                                                id="document_type" name="document_type" required>
                                            <option value="">Seleccionar</option>
                                            <option value="DNI" {{ old('document_type', $customer->document_type) == 'DNI' ? 'selected' : '' }}>DNI</option>
                                            <option value="RUC" {{ old('document_type', $customer->document_type) == 'RUC' ? 'selected' : '' }}>RUC</option>
                                            <option value="PASSPORT" {{ old('document_type', $customer->document_type) == 'PASSPORT' ? 'selected' : '' }}>Pasaporte</option>
                                            <option value="OTHER" {{ old('document_type', $customer->document_type) == 'OTHER' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                        @error('document_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="document_number" class="form-label">Número de Documento <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('document_number') is-invalid @enderror" 
                                               id="document_number" name="document_number" value="{{ old('document_number', $customer->document_number) }}" required>
                                        @error('document_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $customer->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $customer->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información adicional -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Dirección y Datos Adicionales</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Dirección</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3">{{ old('address', $customer->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="city" class="form-label">Ciudad</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                               id="city" name="city" value="{{ old('city', $customer->city) }}">
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="postal_code" class="form-label">Código Postal</label>
                                        <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                               id="postal_code" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}">
                                        @error('postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="balance" class="form-label">Balance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" 
                                               class="form-control @error('balance') is-invalid @enderror" 
                                               id="balance" name="balance" 
                                               value="{{ old('balance', $customer->balance) }}" readonly>
                                    </div>
                                    @error('balance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">El balance se actualiza automáticamente con las ventas y pagos</small>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1" 
                                           {{ old('status', $customer->status) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">Cliente Activo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Notas Adicionales</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $customer->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Actualizar Cliente
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
        // Validación de documento según tipo
        document.getElementById('document_type').addEventListener('change', function() {
            const documentType = this.value;
            const documentInput = document.getElementById('document_number');
            
            if (documentType === 'DNI') {
                documentInput.setAttribute('maxlength', '8');
                documentInput.setAttribute('pattern', '[0-9]{8}');
                documentInput.setAttribute('title', 'El DNI debe contener 8 dígitos numéricos');
            } else if (documentType === 'RUC') {
                documentInput.setAttribute('maxlength', '11');
                documentInput.setAttribute('pattern', '[0-9]{11}');
                documentInput.setAttribute('title', 'El RUC debe contener 11 dígitos numéricos');
            } else {
                documentInput.removeAttribute('maxlength');
                documentInput.removeAttribute('pattern');
                documentInput.removeAttribute('title');
            }
        });
        
        // Trigger para inicializar con el valor actual
        document.getElementById('document_type').dispatchEvent(new Event('change'));
    });
</script>
@endpush
