<?php

// Configurar el entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;

// Crear clientes de muestra
$customers = [
    [
        'name' => 'Juan Pérez',
        'email' => 'juan.perez@ejemplo.com',
        'phone' => '999-888-777',
        'address' => 'Av. Principal 123, Ciudad',
        'document_type' => 'DNI',
        'document_number' => '12345678',
        'balance' => 0,
        'status' => true
    ],
    [
        'name' => 'María González',
        'email' => 'maria.gonzalez@ejemplo.com',
        'phone' => '777-666-555',
        'address' => 'Calle Secundaria 456, Ciudad',
        'document_type' => 'DNI',
        'document_number' => '87654321',
        'balance' => 0,
        'status' => true
    ],
    [
        'name' => 'Empresa ABC S.A.',
        'email' => 'contacto@empresaabc.com',
        'phone' => '111-222-333',
        'address' => 'Zona Industrial 789, Ciudad',
        'document_type' => 'RUC',
        'document_number' => '20123456789',
        'balance' => 0,
        'status' => true
    ],
    [
        'name' => 'Carlos Rodríguez',
        'email' => 'carlos.rodriguez@ejemplo.com',
        'phone' => '444-555-666',
        'address' => 'Jr. Los Pinos 987, Ciudad',
        'document_type' => 'DNI',
        'document_number' => '45678912',
        'balance' => 0,
        'status' => true
    ],
    [
        'name' => 'Ana Torres',
        'email' => 'ana.torres@ejemplo.com',
        'phone' => '222-333-444',
        'address' => 'Av. Las Flores 654, Ciudad',
        'document_type' => 'PASAPORTE',
        'document_number' => 'AB123456',
        'balance' => 0,
        'status' => true
    ]
];

foreach ($customers as $customerData) {
    // Verificar si ya existe el cliente por email
    $exists = Customer::where('email', $customerData['email'])->exists();
    
    if (!$exists) {
        Customer::create($customerData);
        echo "Cliente '{$customerData['name']}' creado correctamente.\n";
    } else {
        echo "El cliente '{$customerData['name']}' ya existe.\n";
    }
}

echo "\nSe han creado los clientes de muestra correctamente.";
