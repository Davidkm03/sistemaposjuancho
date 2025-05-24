<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Permission;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * Check if the user has admin role
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    /**
     * Check if the user has cashier role
     *
     * @return bool
     */
    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }
    
    /**
     * Check if the user has the specified role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
    
    /**
     * Relación con permisos
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
                    ->withTimestamps();
    }
    
    /**
     * Verificar si el usuario tiene un permiso específico
     *
     * @param string $permissionSlug
     * @return bool
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Los administradores tienen todos los permisos
        if ($this->isAdmin()) {
            return true;
        }
        
        // Verificar si el usuario tiene el permiso específico
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }
    
    /**
     * Verificar si el usuario tiene alguno de los permisos especificados
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions): bool
    {
        // Los administradores tienen todos los permisos
        if ($this->isAdmin()) {
            return true;
        }
        
        // Verificar si el usuario tiene alguno de los permisos
        return $this->permissions()->whereIn('slug', $permissions)->exists();
    }
    
    /**
     * Asignar permisos al usuario
     *
     * @param array $permissions
     * @return void
     */
    public function givePermissions(array $permissions): void
    {
        $this->permissions()->syncWithoutDetaching($permissions);
    }
    
    /**
     * Revocar permisos al usuario
     *
     * @param array $permissions
     * @return void
     */
    public function revokePermissions(array $permissions): void
    {
        $this->permissions()->detach($permissions);
    }
    
    /**
     * Sincronizar permisos del usuario (reemplaza todos los permisos existentes)
     *
     * @param array $permissions
     * @return void
     */
    public function syncPermissions(array $permissions): void
    {
        $this->permissions()->sync($permissions);
    }
}
