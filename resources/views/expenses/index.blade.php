@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Ingresos y Gastos</h1>
        <a href="{{ route('expenses.create') }}" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Registrar Nueva Transacción
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('expenses.index') }}" method="GET" class="form-inline">
                <div class="form-group mx-sm-3 mb-2">
                    <label for="type" class="sr-only">Tipo</label>
                    <select class="form-control" id="type" name="type">
                        <option value="">Todos los tipos</option>
                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Ingresos</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Gastos</option>
                    </select>
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label for="category" class="sr-only">Categoría</label>
                    <select class="form-control" id="category" name="category">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary mb-2 ml-2">Limpiar</a>
            </form>
        </div>
    </div>

    <!-- Resumen financiero -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Ingresos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format(\App\Models\AccountingTransaction::where('type', 'income')->sum('amount'), 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Gastos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format(\App\Models\AccountingTransaction::where('type', 'expense')->sum('amount'), 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Balance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $income = \App\Models\AccountingTransaction::where('type', 'income')->sum('amount');
                                    $expense = \App\Models\AccountingTransaction::where('type', 'expense')->sum('amount');
                                    $balance = $income - $expense;
                                @endphp
                                ${{ number_format($balance, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de transacciones -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Transacciones</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Opciones:</div>
                    <a class="dropdown-item" href="{{ route('expenses.create') }}"><i class="fas fa-plus fa-sm fa-fw mr-2 text-gray-400"></i> Nueva transacción</a>
                    <a class="dropdown-item" href="#"><i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i> Exportar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 15%">Fecha</th>
                            <th class="text-center" style="width: 30%">Descripción</th>
                            <th class="text-center" style="width: 15%">Categoría</th>
                            <th class="text-center" style="width: 10%">Tipo</th>
                            <th class="text-center" style="width: 15%">Monto</th>
                            <th class="text-center" style="width: 15%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr class="{{ $transaction->type == 'income' ? 'table-success-soft' : 'table-danger-soft' }}">
                                <td class="text-center">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td class="text-center">{{ $transaction->category }}</td>
                                <td class="text-center">
                                    @if($transaction->type == 'income')
                                        <span class="badge bg-success text-white py-1 px-2">Ingreso</span>
                                    @else
                                        <span class="badge bg-danger text-white py-1 px-2">Gasto</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold {{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">${{ number_format($transaction->amount, 2) }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('expenses.show', $transaction->id) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('expenses.edit', $transaction->id) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('expenses.destroy', $transaction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar esta transacción?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3 d-block"></i>
                                    <p class="mb-0">No hay transacciones registradas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Navegación de transacciones">
                    <ul class="pagination">
                        {{-- Botón Anterior --}}
                        @if ($transactions->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $transactions->previousPageUrl() }}" rel="prev">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Números de página --}}
                        @for ($i = 1; $i <= $transactions->lastPage(); $i++)
                            <li class="page-item {{ $transactions->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" href="{{ $transactions->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor

                        {{-- Botón Siguiente --}}
                        @if ($transactions->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $transactions->nextPageUrl() }}" rel="next">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            
            <style>
                /* Estilos personalizados para la paginación */
                .pagination {
                    margin-bottom: 0;
                }
                .pagination .page-item .page-link {
                    color: #4e73df;
                    padding: 0.5rem 0.75rem;
                    font-size: 0.875rem;
                    line-height: 1.5;
                }
                .pagination .page-item.active .page-link {
                    background-color: #4e73df;
                    border-color: #4e73df;
                    color: white;
                }
                .pagination .page-item .page-link:hover {
                    background-color: #eaecf4;
                }
                
                /* Estilos para filas de ingresos y gastos */
                .table-success-soft {
                    background-color: rgba(40, 167, 69, 0.05);
                }
                .table-danger-soft {
                    background-color: rgba(220, 53, 69, 0.05);
                }
                
                /* Mejorar estilo de botones en grupo */
                .btn-group .btn {
                    margin: 0 2px;
                }
                
                /* Mejorar estilo de los badges */
                .badge {
                    font-weight: 500;
                    font-size: 0.8rem;
                    border-radius: 30px;
                    padding: 0.25rem 0.75rem;
                }
            </style>
        </div>
    </div>
</div>
@endsection
