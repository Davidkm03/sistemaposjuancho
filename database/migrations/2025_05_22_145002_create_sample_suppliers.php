<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Supplier;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear proveedores de ejemplo
        $suppliers = [
            [
                'name' => 'Distribuidora Tecnológica ABC',
                'email' => 'ventas@distribuidoraabc.com',
                'phone' => '555-123-4567',
                'address' => 'Av. Tecnología 123, Ciudad Ejemplo',
                'contact_person' => 'Juan Pérez',
                'tax_number' => 'ABC12345',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Mayorista de Computación XYZ',
                'email' => 'info@mayoristaxyz.com',
                'phone' => '555-987-6543',
                'address' => 'Calle Informática 456, Ciudad Ejemplo',
                'contact_person' => 'María Rodríguez',
                'tax_number' => 'XYZ67890',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Suministros Electrónicos Globales',
                'email' => 'ventas@suministrosglobales.com',
                'phone' => '555-456-7890',
                'address' => 'Plaza Digital 789, Ciudad Ejemplo',
                'contact_person' => 'Carlos Gómez',
                'tax_number' => 'SEG45678',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Importadora Tecnológica Internacional',
                'email' => 'contacto@importadoratech.com',
                'phone' => '555-234-5678',
                'address' => 'Blvd. Innovación 321, Ciudad Ejemplo',
                'contact_person' => 'Ana López',
                'tax_number' => 'ITI98765',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Proveedores de Hardware Unidos',
                'email' => 'ventas@proveedoreshardware.com',
                'phone' => '555-876-5432',
                'address' => 'Paseo de los Componentes 654, Ciudad Ejemplo',
                'contact_person' => 'Roberto Martínez',
                'tax_number' => 'PHU54321',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insertar los proveedores
        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar los proveedores de ejemplo
        $taxNumbers = ['ABC12345', 'XYZ67890', 'SEG45678', 'ITI98765', 'PHU54321'];
        Supplier::whereIn('tax_number', $taxNumbers)->delete();
    }
};
