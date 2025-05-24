@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalles del Usuario</h1>
        <div>
            @if($user->id !== auth()->id())
                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-1"></i> Editar
                </a>
            @endif
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Información del Usuario -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Información Personal</h6>
                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-success' }}">
                        {{ $user->role === 'admin' ? 'Administrador' : 'Cajero' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4e73df&color=fff&size=128" 
                             alt="{{ $user->name }}" class="rounded-circle img-fluid" style="width: 128px;">
                        <h4 class="mt-3">{{ $user->name }}</h4>
                        
                        @if(!$user->isAdmin())
                            <div class="mt-3">
                                <a href="{{ route('users.permissions.edit', $user) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-key mr-1"></i> Gestionar Permisos
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Email:</span>
                            <span>{{ $user->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Rol:</span>
                            <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-success' }}">
                                {{ $user->role === 'admin' ? 'Administrador' : 'Cajero' }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Creado:</span>
                            <span>{{ $user->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Última actualización:</span>
                            <span>{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <!-- Permisos y Accesos -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Permisos y Accesos</h6>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $user->role === 'admin' ? 'Administrador' : 'Cajero' }}</h5>
                    
                    @if($user->role === 'admin')
                        <p class="card-text">Como administrador, este usuario tiene acceso completo al sistema:</p>
                        <ul class="mb-4">
                            <li>Gestión completa de productos y categorías</li>
                            <li>Administración de clientes y proveedores</li>
                            <li>Acceso a reportes financieros y contabilidad</li>
                            <li>Gestión de usuarios y asignación de roles</li>
                            <li>Cancelación y modificación de ventas</li>
                            <li>Acceso a configuración del sistema</li>
                        </ul>
                    @else
                        <p class="card-text">Como cajero, este usuario tiene acceso limitado al sistema:</p>
                        <ul class="mb-4">
                            <li>Uso del punto de venta (POS)</li>
                            <li>Visualización y creación de clientes</li>
                            <li>Registro y visualización de ventas</li>
                            <li>Impresión de recibos</li>
                        </ul>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i>
                            Los cajeros no pueden modificar productos, categorías, ni realizar acciones administrativas.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
