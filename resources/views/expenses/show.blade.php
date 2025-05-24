@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalle de Transacción</h1>
        <div>
            <a href="{{ route('expenses.edit', $transaction->id) }}" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Editar
            </a>
            <a href="{{ route('expenses.index') }}" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver al listado
            </a>
        </div>
    </div>

    <!-- Detalle de transacción -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Información de la Transacción</h6>
            <div>
                @if($transaction->type == 'income')
                    <span class="badge badge-success p-2">Ingreso</span>
                @else
                    <span class="badge badge-danger p-2">Gasto</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Monto</h5>
                        <p class="h4 text-primary">${{ number_format($transaction->amount, 2) }}</p>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Fecha</h5>
                        <p>{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Descripción</h5>
                        <p>{{ $transaction->description }}</p>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Categoría</h5>
                        <p>{{ $transaction->category }}</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Notas</h5>
                        <p>{{ $transaction->notes ?? 'No hay notas adicionales' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="border-top pt-3 mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h5 class="font-weight-bold">Creado por</h5>
                            <p>{{ $transaction->createdBy ? $transaction->createdBy->name : 'Sistema' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h5 class="font-weight-bold">Última modificación</h5>
                            <p>{{ $transaction->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <form action="{{ route('expenses.destroy', $transaction->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta transacción?')">
                        <i class="fas fa-trash"></i> Eliminar Transacción
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
