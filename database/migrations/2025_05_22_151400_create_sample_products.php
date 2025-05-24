<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar si ya existen categorías, si no, crear algunas
        if (Category::count() == 0) {
            $categories = [
                [
                    'name' => 'Hardware',
                    'description' => 'Componentes físicos de computadoras',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'Software',
                    'description' => 'Programas y aplicaciones informáticas',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'Periféricos',
                    'description' => 'Dispositivos externos para computadoras',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'Redes',
                    'description' => 'Equipos y accesorios de redes',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
            ];

            foreach ($categories as $category) {
                Category::create($category);
            }
        }

        // Crear productos de prueba
        $products = [
            [
                'name' => 'Laptop HP Pavilion',
                'code' => 'HP-PAV-001',
                'description' => 'Laptop HP Pavilion, 16GB RAM, 512GB SSD, Intel Core i7',
                'purchase_price' => 800.00,
                'selling_price' => 1200.00,
                'stock' => 15,
                'min_stock' => 3,
                'category_id' => 1, // Hardware
                'supplier_id' => 1, // Primer proveedor
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Monitor Samsung 27"',
                'code' => 'SM-MON-27',
                'description' => 'Monitor Samsung 27 pulgadas 4K Ultra HD',
                'purchase_price' => 250.00,
                'selling_price' => 350.00,
                'stock' => 20,
                'min_stock' => 5,
                'category_id' => 3, // Periféricos
                'supplier_id' => 3, // Tercer proveedor
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Teclado Mecánico Logitech',
                'code' => 'LOG-KB-01',
                'description' => 'Teclado mecánico Logitech RGB con switches Blue',
                'purchase_price' => 80.00,
                'selling_price' => 120.00,
                'stock' => 30,
                'min_stock' => 8,
                'category_id' => 3, // Periféricos
                'supplier_id' => 2, // Segundo proveedor
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Mouse Inalámbrico',
                'code' => 'MOUSE-W01',
                'description' => 'Mouse inalámbrico ergonómico con 6 botones',
                'purchase_price' => 15.00,
                'selling_price' => 25.00,
                'stock' => 50,
                'min_stock' => 10,
                'category_id' => 3, // Periféricos
                'supplier_id' => 2, // Segundo proveedor
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Windows 11 Pro',
                'code' => 'WIN11-PRO',
                'description' => 'Licencia de Windows 11 Pro 64 bits',
                'purchase_price' => 120.00,
                'selling_price' => 199.99,
                'stock' => 100,
                'min_stock' => 20,
                'category_id' => 2, // Software
                'supplier_id' => 4, // Cuarto proveedor
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Office 365',
                'code' => 'OFF-365',
                'description' => 'Microsoft Office 365 suscripción anual',
                'purchase_price' => 70.00,
                'selling_price' => 99.99,
                'stock' => 80,
                'min_stock' => 15,
                'category_id' => 2, // Software
                'supplier_id' => 4, // Cuarto proveedor
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Router WiFi 6',
                'code' => 'WIFI6-RTR',
                'description' => 'Router WiFi 6 de doble banda con 4 antenas',
                'purchase_price' => 90.00,
                'selling_price' => 149.99,
                'stock' => 25,
                'min_stock' => 5,
                'category_id' => 4, // Redes
                'supplier_id' => 1, // Primer proveedor
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Cable HDMI 2m',
                'code' => 'HDMI-2M',
                'description' => 'Cable HDMI 2.1 de 2 metros',
                'purchase_price' => 5.00,
                'selling_price' => 12.99,
                'stock' => 100,
                'min_stock' => 20,
                'category_id' => 3, // Periféricos
                'supplier_id' => 3, // Tercer proveedor
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Memoria RAM 16GB',
                'code' => 'RAM-16GB',
                'description' => 'Memoria RAM DDR4 16GB 3200MHz',
                'purchase_price' => 60.00,
                'selling_price' => 89.99,
                'stock' => 40,
                'min_stock' => 10,
                'category_id' => 1, // Hardware
                'supplier_id' => 1, // Primer proveedor
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Disco SSD 1TB',
                'code' => 'SSD-1TB',
                'description' => 'Disco de estado sólido SSD 1TB SATA',
                'purchase_price' => 100.00,
                'selling_price' => 159.99,
                'stock' => 30,
                'min_stock' => 5,
                'category_id' => 1, // Hardware
                'supplier_id' => 3, // Tercer proveedor
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insertar los productos
        foreach ($products as $product) {
            Product::create($product);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar los productos de muestra
        $codes = [
            'HP-PAV-001', 'SM-MON-27', 'LOG-KB-01', 'MOUSE-W01', 'WIN11-PRO',
            'OFF-365', 'WIFI6-RTR', 'HDMI-2M', 'RAM-16GB', 'SSD-1TB'
        ];
        
        Product::whereIn('code', $codes)->delete();
    }
};
