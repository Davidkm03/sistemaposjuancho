<?php

// Configurar el entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;

// Crear categorías de muestra
$categories = [
    [
        'name' => 'Electrónicos',
        'description' => 'Productos electrónicos como teléfonos, computadoras, etc.',
        'status' => true
    ],
    [
        'name' => 'Ropa',
        'description' => 'Todo tipo de prendas de vestir',
        'status' => true
    ],
    [
        'name' => 'Alimentos',
        'description' => 'Productos alimenticios y bebidas',
        'status' => true
    ],
    [
        'name' => 'Hogar',
        'description' => 'Artículos para el hogar y decoración',
        'status' => true
    ],
    [
        'name' => 'Juguetes',
        'description' => 'Juguetes para niños de todas las edades',
        'status' => true
    ]
];

foreach ($categories as $categoryData) {
    // Verificar si ya existe la categoría
    $exists = Category::where('name', $categoryData['name'])->exists();
    
    if (!$exists) {
        Category::create($categoryData);
        echo "Categoría '{$categoryData['name']}' creada correctamente.\n";
    } else {
        echo "La categoría '{$categoryData['name']}' ya existe.\n";
    }
}

echo "\nSe han creado las categorías de muestra correctamente.";
