<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPermissionController extends Controller
{
    /**
     * Constructor que asegura que solo administradores puedan acceder
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tienes permiso para gestionar permisos de usuarios. Se requiere rol de administrador.');
            }
            return $next($request);
        });
    }
    
    /**
     * Mostrar formulario para editar permisos de un usuario
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // No permitir editar los permisos de un administrador (ya tiene todos)
        if ($user->isAdmin()) {
            return redirect()->route('users.show', $user)
                ->with('info', 'Los usuarios administradores tienen todos los permisos por defecto.');
        }
        
        // Obtener todos los permisos agrupados
        $permissionsByGroup = Permission::orderBy('group')->get()->groupBy('group');
        
        // Obtener IDs de permisos asignados al usuario
        $userPermissionIds = $user->permissions->pluck('id')->toArray();
        
        return view('users.permissions', compact('user', 'permissionsByGroup', 'userPermissionIds'));
    }
    
    /**
     * Actualizar permisos de un usuario
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // No permitir editar los permisos de un administrador (ya tiene todos)
        if ($user->isAdmin()) {
            return redirect()->route('users.show', $user)
                ->with('info', 'Los usuarios administradores tienen todos los permisos por defecto.');
        }
        
        // Validar datos
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        // Obtener los permisos seleccionados
        $permissionIds = $request->input('permissions', []);
        
        // Sincronizar permisos del usuario
        $user->permissions()->sync($permissionIds);
        
        return redirect()->route('users.show', $user)
            ->with('success', 'Permisos actualizados correctamente.');
    }
}
