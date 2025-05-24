<?php

use App\Http\Controllers\AccountingTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosticController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesGoalController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TempGoalsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPermissionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Ruta de inicio redirige al dashboard para usuarios autenticados o a la página de inicio de sesión
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
});

// Rutas de autenticación
Auth::routes(['register' => false]); // Desactivar registro público por seguridad

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth'])->group(function () {
    // Dashboard - Accesible para todos los usuarios autenticados
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rutas accesibles para cajeros y administradores
    Route::middleware(['auth'])->group(function () {
        // POS - Para realizar ventas
        Route::get('/pos', [SaleController::class, 'pos'])->name('pos');
        
        // API para obtener productos en POS
        Route::get('/api/products', [ProductController::class, 'getProduct'])->name('api.products.get');
        
        // Gestión básica de ventas - cajeros pueden crear y ver ventas
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::post('/sales/{sale}/complete', [SaleController::class, 'complete'])->name('sales.complete');
        Route::get('/sales/{sale}/print-invoice', [SaleController::class, 'printInvoice'])->name('sales.print-invoice');
        
        // Gestión básica de clientes - cajeros pueden ver y crear clientes
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        
        // API para registro rápido de gastos/ingresos desde POS
        Route::post('/api/expenses', [ExpenseController::class, 'storeFromPos'])->name('api.expenses.store');
    });
    
    // Rutas accesibles por usuarios autenticados (el control de roles se hace en cada controlador)
    Route::middleware(['auth'])->group(function () {
        // Gestión completa de productos
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.update-stock');
        
        // Gestión completa de categorías
        Route::resource('categories', CategoryController::class);
        
        // Gestión avanzada de clientes - administradores pueden editar y eliminar
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        
        // Gestión completa de proveedores
        Route::resource('suppliers', SupplierController::class);
        
        // Gestión avanzada de ventas - administradores pueden editar, cancelar y eliminar
        Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit');
        Route::put('/sales/{sale}', [SaleController::class, 'update'])->name('sales.update');
        Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::get('/sales/{sale}/invoice', [SaleController::class, 'generateInvoice'])->name('sales.invoice');
        
        // Contabilidad - solo administradores
        Route::resource('accounting', AccountingTransactionController::class);
        Route::get('/reports', [AccountingTransactionController::class, 'reports'])->name('accounting.reports');
        
        // Gestión de usuarios - solo administradores
        Route::resource('users', UserController::class);
        
        // Gestión de permisos de usuarios
        Route::get('/users/{user}/permissions', [UserPermissionController::class, 'edit'])->name('users.permissions.edit');
        Route::put('/users/{user}/permissions', [UserPermissionController::class, 'update'])->name('users.permissions.update');
        
        // Gestión de ingresos y gastos
        Route::resource('expenses', ExpenseController::class);
        
    });
    
    // Sistema de metas de ventas (el control de acceso se maneja en el controlador)
    Route::resource('goals', SalesGoalController::class);
    Route::get('/goals/{goal}/regenerate', [SalesGoalController::class, 'regenerateRecommendations'])->name('goals.regenerate');
    
    // Widget de metas para el POS (accesible para todos los usuarios autenticados)
    Route::get('/pos/goals-widget', [SalesGoalController::class, 'posWidget'])->name('pos.goals-widget');
    Route::get('/pos/goals-combo/{comboId}', [SalesGoalController::class, 'posRecommendations'])->name('pos.goals-combo');
    
    // Diagnóstico del sistema de metas (solo para resolver problemas)
    Route::get('/diagnostic/goals', [DiagnosticController::class, 'diagnoseGoals'])->name('diagnostic.goals');
    Route::get('/diagnostic/force-update-goals', [DiagnosticController::class, 'forceUpdateGoals'])->name('diagnostic.force-update-goals');
    
    // Rutas adicionales para metas
    Route::post('/goals/{goal}/update', [SalesGoalController::class, 'update'])->name('goals.update.post');
    Route::post('/goals/store-direct', [SalesGoalController::class, 'storeDirect'])->name('goals.store.direct');
});
