@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- Productos y categorías (lado izquierdo) -->
        <div class="col-lg-7 border-end">
            <div class="p-3 bg-white shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Punto de Venta</h5>
                    <div>
                        <button type="button" class="btn btn-outline-danger btn-sm me-1" id="openExpenseModalBtn" title="Registrar gasto o ingreso">
                            <i class="fas fa-money-bill-wave"></i> Registrar Gasto/Ingreso
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="{{ route('sales.index') }}" class="btn btn-outline-primary btn-sm ms-1">
                            <i class="fas fa-shopping-cart"></i> Ventas
                        </a>
                    </div>
                </div>
                
                <!-- Búsqueda de Productos -->
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchProduct" class="form-control" placeholder="Buscar producto por nombre o código">
                    <button type="button" id="barcodeButton" class="btn btn-outline-secondary">
                        <i class="fas fa-barcode"></i>
                    </button>
                </div>
                
                <!-- Categorías -->
                <div class="mb-3 categories-wrapper">
                    <div class="d-flex align-items-center mb-2">
                        <h6 class="mb-0 me-2">Categorías:</h6>
                        <div class="categories-scroll">
                            <button class="btn btn-sm btn-primary category-btn active" data-category="all">Todas</button>
                            @foreach($categories ?? [] as $category)
                                <button class="btn btn-sm btn-outline-primary category-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Productos Grid -->
                <div class="row g-2 products-container">
                    @foreach($products ?? [] as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 product-item" data-category="{{ $product->category_id }}" data-search="{{ strtolower($product->name) }} {{ strtolower($product->code) }}">
                            <div class="card h-100 product-card" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock }}">
                                <div class="card-body p-2 text-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid product-img mb-2">
                                    @else
                                        <div class="product-placeholder mb-2">
                                            <i class="fas fa-box fa-3x text-secondary"></i>
                                        </div>
                                    @endif
                                    <h6 class="card-title mb-1">{{ $product->name }}</h6>
                                    <div class="small text-muted mb-1">Stock: {{ $product->stock }}</div>
                                    <div class="fw-bold text-primary">${{ number_format($product->selling_price, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Carrito y Pago (lado derecho) -->
        <div class="col-lg-5">
            <div class="p-3 bg-white shadow-sm h-100 d-flex flex-column">
                <!-- Cliente y datos de la venta -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="mb-3">
                                <label for="customerSelect" class="form-label">Cliente</label>
                                <div class="input-group">
                                    <select class="form-select" id="customerSelect" name="customer_id">
                                        <option value="">Cliente Casual</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->document_number }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#newCustomerModal">
                                        <i class="fas fa-plus"></i> Nuevo
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Factura #</label>
                                <input type="text" id="invoiceNumber" class="form-control form-control-sm" value="{{ $invoiceNumber ?? 'INV-' . date('Ymd') . '-0001' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabla de productos seleccionados -->
                <div class="flex-grow-1 mb-3 overflow-auto" style="max-height: calc(100vh - 400px);">
                    <table class="table table-sm table-hover" id="cartTable">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los productos se agregarán dinámicamente con JavaScript -->
                            <tr id="emptyCartRow">
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                    <p>El carrito está vacío</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Resumen y Totales -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label small">Subtotal</label>
                                <input type="text" id="subtotal" class="form-control form-control-sm text-end" value="$0.00" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">IVA</label>
                                <div class="d-flex gap-1">
                                    <select id="taxRate" class="form-select form-select-sm">
                                        <option value="0">Exento (0%)</option>
                                        <option value="5">Reducido (5%)</option>
                                        <option value="19" selected>General (19%)</option>
                                        <option value="custom">Personalizado</option>
                                    </select>
                                    <input type="number" id="customTaxRate" class="form-control form-control-sm" value="19" min="0" max="100" style="width: 70px; display: none;">
                                </div>
                                <div class="small text-end text-muted mt-1">
                                    Valor IVA: <span id="taxValue">$0.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label small">Descuento</label>
                                <input type="number" id="discount" class="form-control form-control-sm text-end" value="0" min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Total</label>
                                <input type="text" id="total" class="form-control form-control-sm text-end fw-bold" value="$0.00" readonly>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small">Método de Pago</label>
                                <select id="paymentMethod" class="form-select form-select-sm">
                                    <option value="cash">Efectivo</option>
                                    <option value="card">Tarjeta</option>
                                    <option value="transfer">Transferencia</option>
                                    <option value="other">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Referencia</label>
                                <input type="text" id="paymentReference" class="form-control form-control-sm" placeholder="Referencia de pago">
                            </div>
                            <!-- Campo de transferencia (aparece solo cuando se selecciona transferencia) -->
                            <div class="col-md-12 mt-2" id="transferMethodContainer" style="display: none;">
                                <label class="form-label small">Método de Transferencia</label>
                                <div class="d-flex gap-2">
                                    <select id="transferMethod" class="form-select form-select-sm">
                                        <option value="nequi">Nequi</option>
                                        <option value="bancolombia">Bancolombia</option>
                                        <option value="daviplata">Daviplata</option>
                                        <option value="nu">Nu</option>
                                        <option value="custom">Otro</option>
                                    </select>
                                    <input type="text" id="customTransferMethod" class="form-control form-control-sm" placeholder="Especificar método" style="display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notas y Botones de Acción -->
                <div class="mb-3">
                    <textarea id="notes" class="form-control" rows="2" placeholder="Notas adicionales para esta venta..."></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button id="clearCart" class="btn btn-outline-danger flex-grow-1">
                        <i class="fas fa-trash"></i> Limpiar
                    </button>
                    <button id="holdSale" class="btn btn-outline-warning flex-grow-1">
                        <i class="fas fa-pause"></i> En Espera
                    </button>
                    <button id="processSale" class="btn btn-success flex-grow-1">
                        <i class="fas fa-check"></i> Procesar Venta
                    </button>
                </div>
                
                <!-- Widget de Metas -->
                <div class="card shadow mt-3" id="goals-widget">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-bullseye me-1"></i> Meta de Ventas
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="showRecommendations">
                            <i class="fas fa-lightbulb me-1"></i> Recomendaciones
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div id="goals-widget-content">
                            <!-- Contenido cargado vía AJAX -->
                            <div class="text-center py-2">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <span class="ms-2">Cargando meta actual...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cantidad -->
<div class="modal fade" id="quantityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cantidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalProductId">
                <div class="mb-3">
                    <label for="modalQuantity" class="form-label">Cantidad:</label>
                    <input type="number" class="form-control" id="modalQuantity" min="1" value="1">
                    <div class="form-text" id="stockInfo"></div>
                </div>
                <div class="mb-3">
                    <label for="modalPrice" class="form-label">Precio:</label>
                    <input type="number" class="form-control" id="modalPrice" min="0" step="0.01">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="addToCartBtn">Agregar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Resultados -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">Venta Procesada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear nuevo cliente -->
<div class="modal fade" id="newCustomerModal" tabindex="-1" aria-labelledby="newCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newCustomerModalLabel">Crear Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newCustomerForm">
                    @csrf
                    <div class="mb-3">
                        <label for="customerName" class="form-label">Nombre Completo*</label>
                        <input type="text" class="form-control" id="customerName" name="name" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="documentType" class="form-label">Tipo de Documento*</label>
                            <select class="form-select" id="documentType" name="document_type" required>
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="CE">Carnet de Extranjería</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="documentNumber" class="form-label">Número de Documento*</label>
                            <input type="text" class="form-control" id="documentNumber" name="document_number" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="customerPhone" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="customerPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="customerEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="customerEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="customerAddress" class="form-label">Dirección</label>
                        <textarea class="form-control" id="customerAddress" name="address" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveCustomerBtn">Guardar Cliente</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar gastos/ingresos -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalLabel">Registrar Transacción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="expenseForm">
                    @csrf
                    <div class="mb-3">
                        <label for="transactionType" class="form-label">Tipo de Transacción*</label>
                        <select class="form-select" id="transactionType" name="type" required>
                            <option value="expense">Gasto</option>
                            <option value="income">Ingreso</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="transactionAmount" class="form-label">Monto*</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="transactionAmount" name="amount" step="0.01" min="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="transactionDescription" class="form-label">Descripción*</label>
                        <input type="text" class="form-control" id="transactionDescription" name="description" required>
                    </div>
                    <div class="mb-3">
                        <label for="transactionCategory" class="form-label">Categoría*</label>
                        <div class="input-group">
                            <select class="form-select" id="transactionCategory" name="category">
                                <option value="">Seleccionar categoría</option>
                                <option value="Compras">Compras</option>
                                <option value="Servicios">Servicios</option>
                                <option value="Salarios">Salarios</option>
                                <option value="Alquiler">Alquiler</option>
                                <option value="Impuestos">Impuestos</option>
                                <option value="Ventas">Ventas</option>
                                <option value="Devoluciones">Devoluciones</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <button class="btn btn-outline-secondary" type="button" id="showCustomCategoryBtn">+</button>
                        </div>
                    </div>
                    <div class="mb-3 d-none" id="customCategoryContainer">
                        <label for="customCategory" class="form-label">Nueva Categoría</label>
                        <input type="text" class="form-control" id="customCategory">
                    </div>
                    <div class="mb-3">
                        <label for="transactionNotes" class="form-label">Notas</label>
                        <textarea class="form-control" id="transactionNotes" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveExpenseBtn">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Recomendaciones de Metas -->
<div class="modal fade" id="recommendationsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Recomendaciones Inteligentes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="recommendations-content">
                    <!-- Contenido cargado vía AJAX -->
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando recomendaciones...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .categories-wrapper {
        position: relative;
    }
    
    .categories-scroll {
        display: flex;
        gap: 5px;
        overflow-x: auto;
        padding-bottom: 5px;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    
    .categories-scroll::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }
    
    .product-card {
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .product-img {
        max-height: 100px;
        object-fit: contain;
    }
    
    .product-placeholder {
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .cart-quantity-btn {
        width: 25px;
        height: 25px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        padding: 0;
    }
</style>
@endpush

@push('scripts')
<!-- Incluir Bootstrap directamente para asegurar que esté disponible -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables para el manejo de métodos de pago
        const paymentMethodSelect = document.getElementById('paymentMethod');
        const transferMethodContainer = document.getElementById('transferMethodContainer');
        const transferMethodSelect = document.getElementById('transferMethod');
        const customTransferMethodInput = document.getElementById('customTransferMethod');
        
        // Inicializar el modal de nuevo cliente
        const newCustomerModal = document.getElementById('newCustomerModal');
        
        // Evento para guardar nuevo cliente
        document.getElementById('saveCustomerBtn').addEventListener('click', function() {
            const form = document.getElementById('newCustomerForm');
            const formData = new FormData(form);
            
            // Verificar que los campos requeridos estén completos
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Crear objeto para enviar como JSON
            const customerData = {};
            formData.forEach((value, key) => {
                customerData[key] = value;
            });
            
            // Enviar solicitud para crear cliente
            fetch('/customers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(customerData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al crear el cliente');
                }
                return response.json();
            })
            .then(data => {
                // Agregar el nuevo cliente al dropdown
                const customerSelect = document.getElementById('customerSelect');
                const option = document.createElement('option');
                option.value = data.id;
                option.text = `${data.name} - ${data.document_number}`;
                option.selected = true;
                customerSelect.appendChild(option);
                
                // Cerrar el modal y mostrar mensaje de éxito
                bootstrap.Modal.getInstance(newCustomerModal).hide();
                alert('Cliente creado exitosamente');
                
                // Limpiar el formulario
                form.reset();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al crear el cliente: ' + error.message);
            });
        });
        
        // Manejar cambios en el método de pago
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', function() {
            if (this.value === 'transfer') {
                if (transferMethodContainer) {
                    transferMethodContainer.style.display = 'block';
                }
            } else {
                if (transferMethodContainer) {
                    transferMethodContainer.style.display = 'none';
                }
            }
        });
    }
    
    // Manejar cambios en el método de transferencia
    if (transferMethodSelect) {
        transferMethodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                if (customTransferMethodInput) {
                    customTransferMethodInput.style.display = 'block';
                    customTransferMethodInput.focus();
                }
            } else {
                if (customTransferMethodInput) {
                    customTransferMethodInput.style.display = 'none';
                }
            }
        });
    }
    
    // Manejar tasas de impuesto personalizadas
    const taxRateSelect = document.getElementById('taxRate');
    const customTaxRateInput = document.getElementById('customTaxRate');
    
    if (taxRateSelect && customTaxRateInput) {
        taxRateSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customTaxRateInput.style.display = 'block';
                customTaxRateInput.focus();
            } else {
                customTaxRateInput.style.display = 'none';
                updateCartTable(); // Actualizar totales con la nueva tasa
            }
        });
        
        customTaxRateInput.addEventListener('input', function() {
            updateCartTable(); // Actualizar totales mientras se edita
        });
    }
    
    // Variables globales
    console.log('Inicializando script POS...');
    let cart = [];
    
    // Asegurarse de que Bootstrap esté disponible
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap no está disponible. Cargando nuevamente...');
        // Cargar Bootstrap de manera dinámica si no está disponible
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';
        script.integrity = 'sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz';
        script.crossOrigin = 'anonymous';
        document.head.appendChild(script);
        
        // Esperar a que se cargue Bootstrap
        script.onload = function() {
            console.log('Bootstrap cargado correctamente');
            initializeModal();
        };
    } else {
        // Bootstrap ya está disponible
        initializeModal();
    }
    
    // Función para inicializar el modal
    function initializeModal() {
        const modalElement = document.getElementById('quantityModal');
        if (modalElement) {
            console.log('Elemento modal encontrado, inicializando...');
            window.quantityModal = new bootstrap.Modal(modalElement);
            console.log('Modal inicializado correctamente');
            
            // Una vez inicializado el modal, inicializar eventos de productos
            initializeProductEvents();
        } else {
            console.error('No se encontró el elemento modal');
        }
    }
    
    // Función para inicializar eventos de productos y categorías
    function initializeProductEvents() {
        // Filtrar por categoría
        const categoryButtons = document.querySelectorAll('.category-btn');
        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-category');
                
                // Activar/desactivar botones
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Mostrar/ocultar productos
                const productItems = document.querySelectorAll('.product-item');
                productItems.forEach(item => {
                    if (categoryId === 'all' || item.getAttribute('data-category') === categoryId) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
        
        // Búsqueda de productos
        const searchInput = document.getElementById('searchProduct');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const productItems = document.querySelectorAll('.product-item');
                
                productItems.forEach(item => {
                    const searchData = item.getAttribute('data-search');
                    if (searchData && searchData.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
        
        // Agregar producto al carrito
        const productCards = document.querySelectorAll('.product-card');
        console.log(`Se encontraron ${productCards.length} tarjetas de productos`);
        
        productCards.forEach(card => {
            card.addEventListener('click', function() {
                console.log('Producto clickeado:', this.getAttribute('data-name'));
                
                const productId = this.getAttribute('data-id');
                const productName = this.getAttribute('data-name');
                const productPrice = parseFloat(this.getAttribute('data-price'));
                const productStock = parseInt(this.getAttribute('data-stock'));
                
                console.log(`Producto: ${productName}, Precio: ${productPrice}, Stock: ${productStock}`);
                
                // Mostrar modal para seleccionar cantidad
                try {
                    const modalProductIdElement = document.getElementById('modalProductId');
                    const modalPriceElement = document.getElementById('modalPrice');
                    const modalQuantityElement = document.getElementById('modalQuantity');
                    const stockInfoElement = document.getElementById('stockInfo');
                    
                    if (modalProductIdElement && modalPriceElement && modalQuantityElement && stockInfoElement) {
                        modalProductIdElement.value = productId;
                        modalPriceElement.value = productPrice.toFixed(2);
                        modalQuantityElement.value = 1;
                        modalQuantityElement.max = productStock;
                        stockInfoElement.textContent = `Stock disponible: ${productStock}`;
                        
                        // Abrir modal usando la variable global
                        if (window.quantityModal) {
                            console.log('Abriendo modal...');
                            window.quantityModal.show();
                        } else {
                            console.error('Modal no inicializado');
                            alert('Error al abrir el modal. Por favor, intenta recargar la página.');
                        }
                    } else {
                        console.error('No se encontraron todos los elementos del modal');
                    }
                } catch (error) {
                    console.error('Error al abrir el modal:', error);
                }
            });
        });
    }
    
    // Agregar al carrito desde el modal
    const addToCartBtn = document.getElementById('addToCartBtn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const modalProductIdElement = document.getElementById('modalProductId');
            const modalQuantityElement = document.getElementById('modalQuantity');
            const modalPriceElement = document.getElementById('modalPrice');
            
            if (!modalProductIdElement || !modalQuantityElement || !modalPriceElement) {
                console.error('No se encontraron los elementos del modal');
                return;
            }
            
            const productId = modalProductIdElement.value;
            const quantity = parseInt(modalQuantityElement.value);
            const price = parseFloat(modalPriceElement.value);
            
            // Validación básica
            if (quantity <= 0 || price <= 0) {
                alert('Por favor ingrese valores válidos');
                return;
            }
            
            // Buscar el producto en el DOM
            const productCard = document.querySelector(`.product-card[data-id="${productId}"]`);
            if (!productCard) {
                console.error('No se encontró la tarjeta del producto');
                return;
            }
            
            const productName = productCard.getAttribute('data-name');
            const productStock = parseInt(productCard.getAttribute('data-stock'));
            
            // Verificar si ya existe en el carrito
            const existingItemIndex = cart.findIndex(item => item.id === productId);
            
            if (existingItemIndex !== -1) {
                // Actualizar cantidad
                const newQuantity = cart[existingItemIndex].quantity + quantity;
                
                if (newQuantity > productStock) {
                    alert(`Stock insuficiente. Solo hay ${productStock} unidades disponibles.`);
                    return;
                }
                
                cart[existingItemIndex].quantity = newQuantity;
                cart[existingItemIndex].price = price;
                cart[existingItemIndex].total = newQuantity * price; // Calcular el total
            } else {
                // Agregar nuevo producto
                cart.push({
                    id: productId,
                    name: productName,
                    quantity: quantity,
                    price: price,
                    stock: productStock,
                    total: quantity * price // Calcular el total
                });
            }
            
            // Actualizar la tabla del carrito
            updateCartTable();
            
            // Cerrar modal con Bootstrap 5 de manera más segura
            try {
                const quantityModalElement = document.getElementById('quantityModal');
                if (quantityModalElement) {
                    const bootstrapModal = bootstrap.Modal.getInstance(quantityModalElement);
                    if (bootstrapModal) {
                        bootstrapModal.hide();
                        console.log('Modal cerrado correctamente con Bootstrap');
                    } else {
                        // Si no existe la instancia, crearla y luego ocultarla
                        const newModal = new bootstrap.Modal(quantityModalElement);
                        newModal.hide();
                        console.log('Modal cerrado con nueva instancia');
                    }
                }
            } catch (error) {
                console.error('Error al cerrar el modal:', error);
                // Plan de respaldo: usar técnica alternativa
                try {
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                    
                    const modalElement = document.getElementById('quantityModal');
                    if (modalElement) {
                        modalElement.classList.remove('show');
                        modalElement.style.display = 'none';
                        modalElement.setAttribute('aria-hidden', 'true');
                        document.body.classList.remove('modal-open');
                    }
                } catch (e) {
                    console.error('Error en el plan de respaldo:', e);
                }
            }
        });
    }
    
    // Actualizar tabla del carrito
    function updateCartTable() {
        const cartTable = document.getElementById('cartTable');
        if (!cartTable) {
            console.error('No se encontró la tabla del carrito');
            return;
        }
        
        const cartTbody = cartTable.querySelector('tbody');
        if (!cartTbody) {
            console.error('No se encontró el tbody de la tabla del carrito');
            return;
        }
        
        const subtotalElement = document.getElementById('subtotal');
        const taxRateElement = document.getElementById('taxRate');
        const customTaxRateElement = document.getElementById('customTaxRate');
        const taxValueElement = document.getElementById('taxValue');
        const totalElement = document.getElementById('total');
        const emptyCartMessage = document.getElementById('emptyCartMessage');
        const paymentMethodElement = document.getElementById('paymentMethod');
        const paymentReferenceElement = document.getElementById('paymentReference');
        
        // Limpiar tabla
        cartTbody.innerHTML = '';
        
        if (cart.length === 0) {
            // Mostrar mensaje de carrito vacío
            if (emptyCartMessage) {
                emptyCartMessage.style.display = '';
            }
            
            // Actualizar totales
            subtotalElement.value = '$0.00';
            taxValueElement.textContent = '$0.00';
            totalElement.value = '$0.00';
            return;
        }
        
        // Ocultar mensaje de carrito vacío
        if (emptyCartMessage) {
            emptyCartMessage.style.display = 'none';
        }
        
        // Calcular totales
        let subtotal = 0;
        
        // Agregar items a la tabla
        cart.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <button class="btn btn-sm btn-outline-secondary cart-quantity-btn me-1 decrease-quantity" data-index="${index}">-</button>
                        <span>${item.quantity}</span>
                        <button class="btn btn-sm btn-outline-secondary cart-quantity-btn ms-1 increase-quantity" data-index="${index}">+</button>
                    </div>
                </td>
                <td class="text-end">$${item.price.toFixed(2)}</td>
                <td class="text-end">$${item.total.toFixed(2)}</td>
                <td class="text-center">
                    <button class="btn btn-sm text-danger remove-item" data-index="${index}">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            cartTbody.appendChild(row);
            subtotal += item.total;
        });
        
        // Obtener tasa de impuesto
        let taxRate = 0.19; // Valor por defecto 19%
        if (taxRateElement) {
            if (taxRateElement.value === 'custom' && customTaxRateElement) {
                const customRate = parseFloat(customTaxRateElement.value || 0);
                if (!isNaN(customRate)) {
                    taxRate = customRate / 100;
                }
            } else {
                const rate = parseFloat(taxRateElement.value || 0);
                if (!isNaN(rate)) {
                    taxRate = rate / 100;
                }
            }
        }
        
        // Calcular impuestos y totales
        const tax = subtotal * taxRate;
        const discount = parseFloat(document.getElementById('discount').value || 0);
        const total = subtotal + tax - discount;
        
        // Actualizar valores en la interfaz
        subtotalElement.value = `$${subtotal.toFixed(2)}`;
        taxValueElement.textContent = `$${tax.toFixed(2)}`;
        totalElement.value = `$${total.toFixed(2)}`;
        
        // Manejar eventos de botones en el carrito
        setupCartButtons();
        
        // Manejar métodos de transferencia si los elementos existen
        if (paymentMethodElement && paymentReferenceElement) {
            paymentMethodElement.addEventListener('change', function() {
                if (this.value === 'transfer') { // Corregido de 'transferencia' a 'transfer'
                    const transferMethodContainer = document.getElementById('transferMethodContainer');
                    if (transferMethodContainer) {
                        transferMethodContainer.style.display = 'block';
                    }
                } else {
                    const transferMethodContainer = document.getElementById('transferMethodContainer');
                    if (transferMethodContainer) {
                        transferMethodContainer.style.display = 'none';
                    }
                }
                
                // Actualizar totales
                updateCartTable();
            });
        }
    }
    
    // Configurar botones del carrito
    function setupCartButtons() {
        document.querySelectorAll('.decrease-quantity').forEach(btn => {
            btn.addEventListener('click', function() {
                const indexAttr = this.getAttribute('data-index');
                if (indexAttr === null) {
                    console.error('Error: data-index no encontrado en el botón');
                    return;
                }
                
                const index = parseInt(indexAttr);
                if (isNaN(index) || !cart[index]) {
                    console.error('Error: índice inválido o producto no encontrado en el carrito');
                    return;
                }
                
                if (cart[index].quantity > 1) {
                    cart[index].quantity--;
                    updateCartTable();
                }
            });
        });
        
        document.querySelectorAll('.increase-quantity').forEach(btn => {
            btn.addEventListener('click', function() {
                const indexAttr = this.getAttribute('data-index');
                if (indexAttr === null) {
                    console.error('Error: data-index no encontrado en el botón');
                    return;
                }
                
                const index = parseInt(indexAttr);
                if (isNaN(index) || !cart[index]) {
                    console.error('Error: índice inválido o producto no encontrado en el carrito');
                    return;
                }
                
                if (cart[index].quantity < cart[index].stock) {
                    cart[index].quantity++;
                    updateCartTable();
                } else {
                    alert('No hay suficiente stock disponible');
                }
            });
        });
        
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const indexAttr = this.getAttribute('data-index');
                if (indexAttr === null) {
                    console.error('Error: data-index no encontrado en el botón');
                    return;
                }
                
                const index = parseInt(indexAttr);
                if (isNaN(index) || index < 0 || index >= cart.length) {
                    console.error('Error: índice inválido para eliminar producto');
                    return;
                }
                
                cart.splice(index, 1);
                updateCartTable();
            });
        });
        
        // Actualizar totales
        updateTotals();
    }
    
    // Actualizar totales
    function updateTotals() {
        let subtotal = 0;
        
        // Calcular el subtotal a partir de los items del carrito
        if (cart && cart.length > 0) {
            cart.forEach(item => {
                if (item && item.quantity && item.price) {
                    subtotal += item.quantity * item.price;
                }
            });
        }
        
        // Obtener tasa de impuesto
        let taxRate = 0.19; // Valor por defecto 19%
        const taxRateElement = document.getElementById('taxRate');
        if (taxRateElement) {
            if (taxRateElement.value === 'custom') {
                const customTaxRateElement = document.getElementById('customTaxRate');
                if (customTaxRateElement && !isNaN(parseFloat(customTaxRateElement.value))) {
                    taxRate = parseFloat(customTaxRateElement.value) / 100;
                }
            } else if (!isNaN(parseFloat(taxRateElement.value))) {
                taxRate = parseFloat(taxRateElement.value) / 100;
            }
        }
        
        // Calcular impuesto, descuento y total
        const tax = subtotal * taxRate;
        const discountElement = document.getElementById('discount');
        const discount = discountElement && !isNaN(parseFloat(discountElement.value)) ? parseFloat(discountElement.value) : 0;
        const total = subtotal + tax - discount;
        
        // Actualizar elementos en la interfaz
        const subtotalElement = document.getElementById('subtotal');
        const totalElement = document.getElementById('total');
        const taxValueElement = document.getElementById('taxValue');
        
        if (subtotalElement) {
            subtotalElement.value = `$${subtotal.toFixed(2)}`;
        }
        if (taxValueElement) {
            taxValueElement.textContent = `$${tax.toFixed(2)}`;
        }
        if (totalElement) {
            totalElement.value = `$${total.toFixed(2)}`;
        }
    }
    
    // Event listeners para los inputs de impuestos y descuentos
    const taxRateElement = document.getElementById('taxRate');
    const customTaxRateElement = document.getElementById('customTaxRate');
    const discountInput = document.getElementById('discount');
    
    if (taxRateElement) {
        taxRateElement.addEventListener('change', updateTotals);
    }
    
    if (customTaxRateElement) {
        customTaxRateElement.addEventListener('input', updateTotals);
    }
    
    if (discountInput) {
        discountInput.addEventListener('input', updateTotals);
    }
    
    // Limpiar carrito
    const clearCartButton = document.getElementById('clearCart');
    if (clearCartButton) {
        clearCartButton.addEventListener('click', function() {
            if (confirm('¿Está seguro de que desea limpiar el carrito?')) {
                cart = [];
                updateCartTable();
            }
        });
    }
    
    // Botón para procesar venta
    const processSaleBtn = document.getElementById('processSale');
    if (processSaleBtn) {
        processSaleBtn.addEventListener('click', function() {
            // Verificar si hay productos en el carrito
            if (cart.length === 0) {
                alert('El carrito está vacío. Agregue productos antes de procesar la venta.');
                return;
            }
            
            // Guardar referencia del botón para actualizar estado
            const processButton = this;
        
            // Obtener datos del formulario con verificación de nulos
            const customerIdElement = document.getElementById('customerId');
            const invoiceNumberElement = document.getElementById('invoiceNumber');
            const paymentMethodElement = document.getElementById('paymentMethod');
            const paymentReferenceElement = document.getElementById('paymentReference');
            const notesElement = document.getElementById('notes');
            
            // Valores predeterminados si los elementos no existen
            const customerId = customerIdElement ? customerIdElement.value : null;
            const invoiceNumber = invoiceNumberElement ? invoiceNumberElement.value : 'INV-' + new Date().getTime();
            const paymentMethod = paymentMethodElement ? paymentMethodElement.value : 'cash';
            const paymentReference = paymentReferenceElement ? paymentReferenceElement.value : '';
            const notes = notesElement ? notesElement.value : '';
            
            // Obtener datos del método de transferencia si aplica
            let transferDetails = '';
            if (paymentMethod === 'transfer') {
                const transferMethodElement = document.getElementById('transferMethod');
                if (transferMethodElement) {
                    const transferMethod = transferMethodElement.value;
                    if (transferMethod === 'custom') {
                        const customTransferMethodElement = document.getElementById('customTransferMethod');
                        if (customTransferMethodElement) {
                            transferDetails = customTransferMethodElement.value;
                        }
                    } else {
                        transferDetails = transferMethod; // nequi, bancolombia, daviplata, nu
                    }
                }
            }
            
            // Calcular totales
            let subtotal = 0;
            cart.forEach(item => {
                subtotal += item.total;
            });
            
            // Obtener tasa de impuesto
            let taxRate = 0.19; // Valor por defecto 19%
            const taxRateSelect = document.getElementById('taxRate');
            if (taxRateSelect) {
                if (taxRateSelect.value === 'custom') {
                    const customTaxRateInput = document.getElementById('customTaxRate');
                    if (customTaxRateInput && customTaxRateInput.value) {
                        const parsedRate = parseFloat(customTaxRateInput.value);
                        if (!isNaN(parsedRate)) {
                            taxRate = parsedRate / 100;
                        }
                    }
                } else {
                    const parsedRate = parseFloat(taxRateSelect.value);
                    if (!isNaN(parsedRate)) {
                        taxRate = parsedRate / 100;
                    }
                }
            }
            
            // Calcular impuestos y totales
            const tax = subtotal * taxRate;
            
            // Obtener el descuento con verificación de nulos
            let discount = 0;
            const discountElement = document.getElementById('discount');
            if (discountElement && discountElement.value) {
                const discountValue = parseFloat(discountElement.value);
                if (!isNaN(discountValue)) {
                    discount = discountValue;
                }
            }
            
            const total = subtotal + tax - discount;
            
            // Preparar datos para enviar en el formato exacto que espera el controlador
            const products = cart.map(item => ({
                id: item.id,
                quantity: item.quantity,
                price: item.price
            }));
            
            const saleData = {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                customer_id: customerId || null,
                invoice_number: invoiceNumber,
                subtotal: subtotal,
                tax: tax,                   // El controlador espera 'tax', no 'tax_rate'
                discount: discount,
                total: total,
                payment_method: paymentMethod,
                payment_reference: paymentReference,
                notes: notes,
                products: products
            };
            
            // Agregar campo de transferencia solo si es relevante
            if (paymentMethod === 'transfer' && transferDetails) {
                saleData.transfer_details = transferDetails;
            }
            
            console.log('Datos de venta a enviar:', saleData);
            
            // Realizar solicitud al servidor
            fetch('/sales', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(saleData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error de servidor: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Limpiar el carrito
                cart = [];
                updateCartTable();
                
                // Actualizar el widget de metas con las ventas nuevas
                loadGoalsWidget();
                
                // Crear un recibo en la página actual
                const receiptContainer = document.createElement('div');
                receiptContainer.className = 'card shadow mb-4';
                
                // Asegurar que todos los valores numéricos sean realmente números
                const taxRate = parseFloat(data.tax_rate) || 19;
                const subtotal = parseFloat(data.subtotal) || 0;
                const tax = parseFloat(data.tax) || 0;
                const total = parseFloat(data.total) || 0;
                const paymentMethod = data.payment_method || 'efectivo';
                
                // Para debug
                console.log('Valores para el recibo:', { 
                    taxRate, 
                    subtotal, 
                    tax, 
                    total, 
                    paymentMethod,
                    originalTotal: data.total,
                    typeOfTotal: typeof data.total
                });
                
                receiptContainer.innerHTML = `
                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-success text-white">
                        <h6 class="m-0 font-weight-bold">Venta #${data.id} - ${data.invoice_number}</h6>
                        <span>Total: $${total.toFixed(2)}</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Fecha:</strong> ${new Date().toLocaleString()}</p>
                                <p><strong>Método de Pago:</strong> ${paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1)}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p><strong>Subtotal:</strong> $${subtotal.toFixed(2)}</p>
                                <p><strong>Impuesto (${taxRate}%):</strong> $${tax.toFixed(2)}</p>
                            </div>
                        </div>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Precio</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.products.map(product => `
                                        <tr>
                                            <td>${product.name}</td>
                                            <td class="text-center">${product.quantity}</td>
                                            <td class="text-end">$${parseFloat(product.price).toFixed(2)}</td>
                                            <td class="text-end">$${parseFloat(product.total).toFixed(2)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">TOTAL</th>
                                        <th class="text-end">$${total.toFixed(2)}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-secondary" onclick="window.open('${data.print_url}', '_blank')">
                                <i class="fas fa-print"></i> Imprimir Ticket
                            </button>
                            <button class="btn btn-primary" onclick="location.reload()">
                                <i class="fas fa-sync"></i> Nueva Venta
                            </button>
                        </div>
                    </div>
                `;
                
                // Mostrar el recibo en la página
                const mainContainer = document.querySelector('.container-fluid');
                mainContainer.innerHTML = '';
                mainContainer.appendChild(receiptContainer);
                
                // Desplazarse al inicio de la página
                window.scrollTo(0, 0);
            })
            .catch(error => {
                console.error('Error:', error);
                alert(`Error al procesar la venta: ${error.message}`);
            });
        });
    }
    // ======== FUNCIONALIDAD PARA EL MODAL DE GASTOS ========
    // Inicializar el modal de gastos
    let expenseModal;
    const expenseModalEl = document.getElementById('expenseModal');
    if (expenseModalEl) {
        expenseModal = new bootstrap.Modal(expenseModalEl);
    }
    
    // Evento para abrir el modal de gastos
    const openExpenseModalBtn = document.getElementById('openExpenseModalBtn');
    if (openExpenseModalBtn) {
        openExpenseModalBtn.addEventListener('click', function() {
            // Limpiar el formulario
            const expenseForm = document.getElementById('expenseForm');
            if (expenseForm) {
                expenseForm.reset();
                document.getElementById('customCategoryContainer').classList.add('d-none');
            }
            
            // Abrir el modal
            if (expenseModal) {
                expenseModal.show();
            }
        });
    }
    
    // Evento para mostrar/ocultar campo de categoría personalizada
    const showCustomCategoryBtn = document.getElementById('showCustomCategoryBtn');
    if (showCustomCategoryBtn) {
        showCustomCategoryBtn.addEventListener('click', function() {
            const customCategoryContainer = document.getElementById('customCategoryContainer');
            if (customCategoryContainer) {
                customCategoryContainer.classList.toggle('d-none');
                
                // Si se muestra el campo personalizado, poner el foco en él
                if (!customCategoryContainer.classList.contains('d-none')) {
                    document.getElementById('customCategory').focus();
                }
            }
        });
    }
    
    // Evento para guardar el gasto/ingreso
    const saveExpenseBtn = document.getElementById('saveExpenseBtn');
    if (saveExpenseBtn) {
        saveExpenseBtn.addEventListener('click', function() {
            const expenseForm = document.getElementById('expenseForm');
            
            // Verificar que los campos requeridos estén completos
            if (!expenseForm.checkValidity()) {
                expenseForm.reportValidity();
                return;
            }
            
            // Obtener datos del formulario
            const type = document.getElementById('transactionType').value;
            const amount = document.getElementById('transactionAmount').value;
            const description = document.getElementById('transactionDescription').value;
            let category = document.getElementById('transactionCategory').value;
            const notes = document.getElementById('transactionNotes').value;
            
            // Verificar si se está usando una categoría personalizada
            const customCategoryContainer = document.getElementById('customCategoryContainer');
            if (!customCategoryContainer.classList.contains('d-none')) {
                const customCategoryValue = document.getElementById('customCategory').value.trim();
                if (customCategoryValue) {
                    category = customCategoryValue;
                }
            }
            
            // Validar la categoría
            if (!category) {
                alert('Por favor seleccione o ingrese una categoría');
                return;
            }
            
            // Preparar datos para enviar
            const expenseData = {
                type: type,
                amount: parseFloat(amount),    // Asegurar que amount sea numérico
                description: description,
                category: category,
                notes: notes
            };
            
            // Mostrar spinner en el botón de guardar
            saveExpenseBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
            saveExpenseBtn.disabled = true;
            
            // Debug para ver qué datos estamos enviando
            console.log('Datos a enviar:', expenseData);
            
            // Obtener el token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log('CSRF Token:', csrfToken);
            
            // Enviar solicitud al servidor usando la nueva ruta API
            fetch('/api/expenses', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(expenseData)
            })
            .then(response => {
                // Capturar el texto de la respuesta para depuración
                return response.text().then(text => {
                    if (!response.ok) {
                        console.error('Respuesta de error completa:', text);
                        throw new Error(`Error de servidor: ${response.status}`);
                    }
                    
                    // Intentar parsear como JSON
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Error al parsear respuesta JSON:', e);
                        console.log('Texto de respuesta:', text);
                        throw new Error('Error al procesar la respuesta del servidor');
                    }
                });
            })
            .then(data => {
                console.log('Respuesta exitosa:', data);
                
                // Ocultar el modal
                if (expenseModal) {
                    expenseModal.hide();
                }
                
                // Mostrar mensaje de éxito
                const typeLabel = type === 'income' ? 'Ingreso' : 'Gasto';
                alert(`${typeLabel} registrado correctamente`);
                
                // Restaurar el botón
                saveExpenseBtn.innerHTML = 'Guardar';
                saveExpenseBtn.disabled = false;
            })
            .catch(error => {
                console.error('Error completo:', error);
                alert(`Error al guardar: ${error.message}`);
                
                // Restaurar el botón
                saveExpenseBtn.innerHTML = 'Guardar';
                saveExpenseBtn.disabled = false;
            });
        });
    }
});
</script>
@endpush

<!-- Modal de Recomendaciones de Metas -->
<div class="modal fade" id="recommendationsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Recomendaciones Inteligentes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="recommendations-content">
                    <!-- Contenido cargado vía AJAX -->
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando recomendaciones...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Cargar widget de metas
    function loadGoalsWidget() {
        console.log('Actualizando widget de metas...');
        $('#goals-widget-content').html(
            '<div class="text-center py-2">'+
            '<div class="spinner-border spinner-border-sm text-primary" role="status">'+
            '<span class="visually-hidden">Cargando...</span>'+
            '</div>'+
            '<span class="ms-2">Cargando meta actual...</span>'+
            '</div>'
        );

        $.ajax({
            url: '{{ route("pos.goals-widget") }}',
            type: 'GET',
            timeout: 10000, // 10 segundos de timeout
            cache: false, // Forzar siempre petición fresca sin caché
            data: { _t: new Date().getTime() }, // Añadir timestamp para evitar caché
            success: function(response) {
                console.log('Meta actualizada recibida.');
                $('#goals-widget-content').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error cargando widget de metas:', status, error);
                $('#goals-widget-content').html(
                    '<div class="alert alert-warning mb-0">'+
                    '<i class="fas fa-exclamation-circle me-1"></i> '+
                    'No se pudo cargar la información de metas. '+
                    '<a href="#" onclick="loadGoalsWidget(); return false;">Reintentar</a>'+
                    '</div>'
                );
            }
        });
    }
    
    // Cargar recomendaciones
    $(document).on('click', '#showRecommendations', function() {
        console.log('Botón de recomendaciones clickeado');
        const goalId = $('#goals-widget-content').find('[data-goal-id]').data('goal-id');
        console.log('ID de meta encontrado:', goalId);
        
        // Mostrar modal siempre
        $('#recommendationsModal').modal('show');
        
        if (goalId) {
            // Mostrar cargando
            $('#recommendations-content').html(
                '<div class="text-center py-3">'+
                '<div class="spinner-border text-primary" role="status">'+
                '<span class="visually-hidden">Cargando...</span>'+
                '</div>'+
                '<p class="mt-2">Cargando recomendaciones...</p>'+
                '</div>'
            );
            
            // Cargar contenido completo de la meta con recomendaciones
            $.ajax({
                url: `/goals/${goalId}`,
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    console.log('Respuesta recibida de la meta');
                    // Extraer solo la sección de recomendaciones
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(response, 'text/html');
                    const recommendations = doc.querySelector('#recommendations-section');
                    
                    if (recommendations) {
                        $('#recommendations-content').html(recommendations.innerHTML);
                        
                        // Agregar funcionalidad a los botones de productos
                        $('.add-product').on('click', function() {
                            const productId = $(this).data('product-id');
                            // Buscar el producto en la cuadrícula y hacer clic en él
                            $(`.product-card[data-id="${productId}"]`).click();
                            $('#recommendationsModal').modal('hide');
                        });
                    } else {
                        $('#recommendations-content').html(
                            '<div class="alert alert-warning">'+
                            '<i class="fas fa-exclamation-circle me-1"></i> '+
                            'No se encontraron recomendaciones para esta meta.'+
                            '</div>'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error cargando recomendaciones:', status, error);
                    $('#recommendations-content').html(
                        '<div class="alert alert-danger">'+
                        '<i class="fas fa-times-circle me-1"></i> '+
                        'Error al cargar las recomendaciones: ' + error + '.'+
                        '</div>'
                    );
                }
            });
        } else {
            $('#recommendations-content').html(
                '<div class="alert alert-info my-3">'+
                '<i class="fas fa-info-circle me-1"></i> '+
                'No hay una meta activa para brindar recomendaciones.<br><br>'+
                'Para crear metas de ventas, acceda al menú <strong>Metas de Ventas</strong> '+
                'desde el panel de administración.'+
                '</div>'
            );
        }
    });
    
    // Cargar al iniciar
    $(document).ready(function() {
        loadGoalsWidget();
        // Actualizar cada 5 minutos
        setInterval(loadGoalsWidget, 300000);
    });
</script>
@endpush
