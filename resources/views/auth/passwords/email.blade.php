@extends('layouts.auth')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Recuperar Contraseña</h2>
        <p class="text-muted mb-0">Ingrese su correo electrónico para recibir un enlace de recuperación</p>
    </div>

    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            
            <div class="mb-4">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="ejemplo@correo.com">
                </div>
                @error('email')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i> Enviar Enlace de Recuperación
                </button>
            </div>
            
            <div class="text-center mt-3">
                <p class="mb-0"><a href="{{ route('login') }}" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Volver al inicio de sesión</a></p>
            </div>
        </form>
    </div>
</div>
@endsection
