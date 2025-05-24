<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('login');
        }
        
        // Si no se especifica un rol, solo verificamos autenticación
        if (!$role) {
            return $next($request);
        }
        
        // Verificar si el usuario tiene el rol requerido
        $userRole = Auth::user()->role;
        
        // Comprobar que el rol del usuario coincide con el requerido
        if ($userRole !== $role) {
            // Si no tiene permiso, redirigir al dashboard con mensaje de error
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta sección. Se requiere rol de ' . ucfirst($role) . '.');
        }
        
        return $next($request);
    }
}
