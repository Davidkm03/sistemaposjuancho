<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar tabla de permisos antes de insertar nuevos
        DB::table('permissions')->truncate();
        
        // Definir grupos de permisos
        $permissionGroups = [
            'dashboard' => [
                ['name' => 'Ver Dashboard', 'slug' => 'dashboard.view', 'description' => 'Ver el panel principal'],
            ],
            'pos' => [
                ['name' => 'Usar POS', 'slug' => 'pos.use', 'description' => 'Usar el punto de venta'],
                ['name' => 'Aplicar Descuentos', 'slug' => 'pos.apply_discounts', 'description' => 'Aplicar descuentos en ventas'],
                ['name' => 'Anular Transacciones', 'slug' => 'pos.void_transactions', 'description' => 'Anular transacciones en curso'],
            ],
            'ventas' => [
                ['name' => 'Ver Ventas', 'slug' => 'sales.view', 'description' => 'Ver listado de ventas'],
                ['name' => 'Crear Ventas', 'slug' => 'sales.create', 'description' => 'Crear nuevas ventas'],
                ['name' => 'Editar Ventas', 'slug' => 'sales.edit', 'description' => 'Editar ventas existentes'],
                ['name' => 'Eliminar Ventas', 'slug' => 'sales.delete', 'description' => 'Eliminar ventas'],
                ['name' => 'Cancelar Ventas', 'slug' => 'sales.cancel', 'description' => 'Cancelar ventas completadas'],
                ['name' => 'Imprimir Facturas', 'slug' => 'sales.print', 'description' => 'Imprimir facturas de ventas'],
            ],
            'clientes' => [
                ['name' => 'Ver Clientes', 'slug' => 'customers.view', 'description' => 'Ver listado de clientes'],
                ['name' => 'Crear Clientes', 'slug' => 'customers.create', 'description' => 'Crear nuevos clientes'],
                ['name' => 'Editar Clientes', 'slug' => 'customers.edit', 'description' => 'Editar clientes existentes'],
                ['name' => 'Eliminar Clientes', 'slug' => 'customers.delete', 'description' => 'Eliminar clientes'],
            ],
            'productos' => [
                ['name' => 'Ver Productos', 'slug' => 'products.view', 'description' => 'Ver listado de productos'],
                ['name' => 'Crear Productos', 'slug' => 'products.create', 'description' => 'Crear nuevos productos'],
                ['name' => 'Editar Productos', 'slug' => 'products.edit', 'description' => 'Editar productos existentes'],
                ['name' => 'Eliminar Productos', 'slug' => 'products.delete', 'description' => 'Eliminar productos'],
                ['name' => 'Actualizar Stock', 'slug' => 'products.update_stock', 'description' => 'Actualizar stock de productos'],
            ],
            'categorias' => [
                ['name' => 'Ver Categorías', 'slug' => 'categories.view', 'description' => 'Ver listado de categorías'],
                ['name' => 'Gestionar Categorías', 'slug' => 'categories.manage', 'description' => 'Crear, editar y eliminar categorías'],
            ],
            'proveedores' => [
                ['name' => 'Ver Proveedores', 'slug' => 'suppliers.view', 'description' => 'Ver listado de proveedores'],
                ['name' => 'Gestionar Proveedores', 'slug' => 'suppliers.manage', 'description' => 'Crear, editar y eliminar proveedores'],
            ],
            'contabilidad' => [
                ['name' => 'Ver Contabilidad', 'slug' => 'accounting.view', 'description' => 'Ver registros contables'],
                ['name' => 'Gestionar Contabilidad', 'slug' => 'accounting.manage', 'description' => 'Crear, editar y eliminar registros contables'],
            ],
            'reportes' => [
                ['name' => 'Ver Reportes', 'slug' => 'reports.view', 'description' => 'Ver reportes y estadísticas'],
                ['name' => 'Exportar Reportes', 'slug' => 'reports.export', 'description' => 'Exportar reportes a Excel/PDF'],
            ],
            'usuarios' => [
                ['name' => 'Ver Usuarios', 'slug' => 'users.view', 'description' => 'Ver listado de usuarios'],
                ['name' => 'Crear Usuarios', 'slug' => 'users.create', 'description' => 'Crear nuevos usuarios'],
                ['name' => 'Editar Usuarios', 'slug' => 'users.edit', 'description' => 'Editar usuarios existentes'],
                ['name' => 'Eliminar Usuarios', 'slug' => 'users.delete', 'description' => 'Eliminar usuarios'],
                ['name' => 'Gestionar Permisos', 'slug' => 'users.manage_permissions', 'description' => 'Asignar permisos a usuarios'],
            ],
            'configuracion' => [
                ['name' => 'Ver Configuración', 'slug' => 'settings.view', 'description' => 'Ver configuración del sistema'],
                ['name' => 'Editar Configuración', 'slug' => 'settings.edit', 'description' => 'Modificar configuración del sistema'],
            ],
        ];
        
        // Crear permisos en la base de datos
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::create([
                    'name' => $permission['name'],
                    'slug' => $permission['slug'],
                    'description' => $permission['description'],
                    'group' => $group,
                ]);
            }
        }
        
        $this->command->info('Permisos creados correctamente.');
    }
}
