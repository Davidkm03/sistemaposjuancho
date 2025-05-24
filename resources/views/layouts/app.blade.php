<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema POS con contabilidad básica">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POS System') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- jQuery (necesario para Ajax y funciones del POS) -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- Bootstrap JS desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer></script>
    
    <!-- jQuery desde CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #224abe;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 10%, var(--secondary-color) 100%);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
        }
        
        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 0 1rem;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
        }
        
        .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link.active {
            color: white;
            font-weight: 600;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link i {
            margin-right: 0.5rem;
            font-size: 1rem;
            width: 1.5rem;
            text-align: center;
        }
        
        .content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .navbar-top {
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .toggle-sidebar {
            background: none;
            border: none;
            color: #6c757d;
            font-size: 1.25rem;
            cursor: pointer;
        }
        
        .user-dropdown .dropdown-toggle {
            color: #6c757d;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .user-dropdown .dropdown-toggle img {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            border: none;
            border-radius: 0.35rem;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h5 {
            margin-bottom: 0;
            color: #4e73df;
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .alert {
            border-radius: 0.35rem;
        }
        
        /* Para pantallas pequeñas */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .content-wrapper {
                margin-left: 0;
            }
            
            .content-wrapper.sidebar-open {
                margin-left: var(--sidebar-width);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div id="app">
        @auth
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-header">
                    <a class="sidebar-brand" href="{{ route('dashboard') }}">
                        <i class="fas fa-cash-register"></i>
                        <span>POS System</span>
                    </a>
                </div>
                
                <hr class="sidebar-divider">
                
                <div class="nav flex-column">
                    <!-- Dashboard - para todos -->
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    
                    <!-- Punto de Venta - para todos -->
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pos') ? 'active' : '' }}" href="{{ route('pos') }}">
                            <i class="fas fa-fw fa-cash-register"></i>
                            <span>Punto de Venta</span>
                        </a>
                    </div>
                    
                    <!-- Ventas - para todos (con limitaciones para cajeros) -->
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                            <i class="fas fa-fw fa-shopping-cart"></i>
                            <span>Ventas</span>
                        </a>
                    </div>
                    
                    <!-- Clientes - para todos (con limitaciones para cajeros) -->
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                            <i class="fas fa-fw fa-users"></i>
                            <span>Clientes</span>
                        </a>
                    </div>
                    
                    <!-- Opciones solo para administradores -->
                    @if(Auth::user()->isAdmin())
                        <div class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                <i class="fas fa-fw fa-box"></i>
                                <span>Productos</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                <i class="fas fa-fw fa-tags"></i>
                                <span>Categorías</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                                <i class="fas fa-fw fa-truck"></i>
                                <span>Proveedores</span>
                            </a>
                        </div>
                        
                        <hr class="sidebar-divider">
                        
                        <div class="nav-item">
                            <a class="nav-link {{ request()->routeIs('accounting.*') ? 'active' : '' }}" href="{{ route('accounting.index') }}">
                                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                                <span>Contabilidad</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                                <i class="fas fa-fw fa-money-bill-alt"></i>
                                <span>Ingresos y Gastos</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a class="nav-link {{ request()->routeIs('goals.*') ? 'active' : '' }}" href="{{ route('goals.index') }}">
                                <i class="fas fa-fw fa-bullseye"></i>
                                <span>Metas de Ventas</span>
                            </a>
                        </div>
                        
                        <div class="nav-item">
                            <a class="nav-link {{ request()->routeIs('accounting.reports') ? 'active' : '' }}" href="{{ route('accounting.reports') }}">
                                <i class="fas fa-fw fa-chart-bar"></i>
                                <span>Reportes</span>
                            </a>
                        </div>
                        
                        <hr class="sidebar-divider">
                        
                        <div class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas fa-fw fa-users-cog"></i>
                                <span>Gestión de Usuarios</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endauth
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            @auth
                <!-- Top Navbar -->
                <nav class="navbar-top">
                    <button class="toggle-sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="user-dropdown dropdown">
                        <a class="dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=4e73df&color=fff" alt="User Avatar">
                            <span>{{ Auth::user()->name }}</span>
                            <span class="badge {{ Auth::user()->role === 'admin' ? 'bg-danger' : 'bg-success' }} ms-1">{{ ucfirst(Auth::user()->role) }}</span>
                        </a>
                        
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Perfil
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Cerrar Sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </nav>
            @endauth
            
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Main Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle functionality
            const toggleButton = document.querySelector('.toggle-sidebar');
            const sidebar = document.querySelector('.sidebar');
            const contentWrapper = document.querySelector('.content-wrapper');
            
            if (toggleButton && sidebar && contentWrapper) {
                toggleButton.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    contentWrapper.classList.toggle('sidebar-open');
                });
            }
            
            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
