<?php

// Configurar el entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Crear un usuario administrador
$admin = User::updateOrCreate(
    ['email' => 'admin@pos.com'],
    [
        'name' => 'Administrador',
        'email' => 'admin@pos.com',
        'password' => Hash::make('admin123'),
        'email_verified_at' => now(),
    ]
);

echo "Usuario administrador creado correctamente:\n";
echo "Email: admin@pos.com\n";
echo "Contraseña: admin123\n";
