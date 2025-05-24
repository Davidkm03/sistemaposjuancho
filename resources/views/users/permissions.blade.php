@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestionar Permisos - {{ $user->name }}</h1>
        <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- Información del Usuario -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Usuario</h6>
                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-success' }}">
                        {{ $user->role === 'admin' ? 'Administrador' : 'Cajero' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4e73df&color=fff&size=128" 
                             alt="{{ $user->name }}" class="rounded-circle img-fluid" style="width: 128px;">
                        <h4 class="mt-3">{{ $user->name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        Selecciona los permisos que deseas asignar a este usuario. Los permisos determinan a qué funcionalidades tiene acceso.
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" id="selectAll" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-check-square mr-1"></i> Seleccionar Todos
                        </button>
                        <button type="button" id="deselectAll" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-square mr-1"></i> Deseleccionar Todos
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Formulario de Permisos -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Permisos Disponibles</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.permissions.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="accordion" id="permissionsAccordion">
                            @foreach($permissionsByGroup as $group => $permissions)
                                <div class="accordion-item mb-3 border">
                                    <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                aria-controls="collapse{{ $loop->index }}">
                                            <i class="fas fa-layer-group mr-2"></i>
                                            {{ ucfirst($group) }}
                                            <span class="badge bg-primary ms-2">{{ $permissions->count() }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                         aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#permissionsAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                @foreach($permissions as $permission)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                                   name="permissions[]" value="{{ $permission->id }}" 
                                                                   id="permission{{ $permission->id }}"
                                                                   {{ in_array($permission->id, $userPermissionIds) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="permission{{ $permission->id }}"
                                                                   title="{{ $permission->description }}">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Guardar Permisos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Botones para seleccionar/deseleccionar todos los permisos
        const selectAllBtn = document.getElementById('selectAll');
        const deselectAllBtn = document.getElementById('deselectAll');
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        
        selectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        });
        
        deselectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        });
    });
</script>
@endpush
