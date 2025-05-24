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
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #224abe;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 15px;
        }
        
        .auth-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .auth-logo i {
            font-size: 3rem;
            color: var(--primary-color);
        }
        
        .auth-logo h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #5a5c69;
            margin-top: 0.5rem;
        }
        
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            overflow: hidden;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1.5rem;
            text-align: center;
        }
        
        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #5a5c69;
            margin: 0;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d3e2;
            font-size: 0.9rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1rem;
            font-weight: 600;
            width: 100%;
            border-radius: 0.5rem;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #858796;
            font-size: 0.875rem;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div id="app">
        <div class="auth-wrapper">
            <div class="auth-logo">
                <i class="fas fa-cash-register"></i>
                <h1>{{ config('app.name', 'POS System') }}</h1>
            </div>
            
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
            
            @yield('content')
            
            <div class="auth-footer">
                &copy; {{ date('Y') }} {{ config('app.name', 'POS System') }}. Todos los derechos reservados.
            </div>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
