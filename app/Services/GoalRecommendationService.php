<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SalesGoal;
use App\Models\AccountingTransaction;
use Illuminate\Support\Facades\DB;

class GoalRecommendationService
{
    /**
     * Genera recomendaciones para una meta específica
     */
    public function generateRecommendations(SalesGoal $goal)
    {
        // Limpiar recomendaciones anteriores
        $goal->productRecommendations()->delete();
        $goal->comboRecommendations()->delete();
        
        // Calcular productos más rentables
        $profitableProducts = $this->identifyProfitableProducts();
        
        // Calcular productos con mejor rotación
        $bestSellingProducts = $this->identifyBestSellingProducts();
        
        // Generar recomendaciones individuales
        $this->generateProductRecommendations($goal, $profitableProducts, $bestSellingProducts);
        
        // Generar recomendaciones de combos
        $this->generateComboRecommendations($goal, $profitableProducts);
        
        return [
            'products' => $goal->productRecommendations()->with('product')->orderBy('priority', 'desc')->get(),
            'combos' => $goal->comboRecommendations()->with('products.product')->orderBy('priority', 'desc')->get()
        ];
    }
    
    /**
     * Identifica los productos con mayor margen de ganancia
     */
    protected function identifyProfitableProducts()
    {
        // Identificar productos con mayor margen de ganancia
        return Product::selectRaw('products.*, (selling_price - purchase_price) as profit_margin')
            ->whereRaw('selling_price > purchase_price')
            ->orderByRaw('(selling_price - purchase_price) desc')
            ->limit(20)
            ->get();
    }
    
    /**
     * Identifica los productos que más se venden
     */
    protected function identifyBestSellingProducts()
    {
        // Obtener productos con mejor historial de ventas
        return Product::select('products.*')
            ->join('sale_details', 'products.id', '=', 'sale_details.product_id')
            ->selectRaw('products.*, COUNT(sale_details.id) as sales_count, SUM(sale_details.quantity) as total_quantity')
            ->groupBy('products.id')
            ->orderByRaw('total_quantity DESC')
            ->limit(20)
            ->get();
    }
    
    /**
     * Genera recomendaciones de productos individuales
     */
    protected function generateProductRecommendations(SalesGoal $goal, $profitableProducts, $bestSellingProducts)
    {
        $remainingAmount = $goal->target_amount - $goal->current_amount;
        $recommendations = [];
        
        // Combinar ambas listas con prioridades
        $prioritizedProducts = $this->prioritizeProducts($profitableProducts, $bestSellingProducts);
        
        foreach ($prioritizedProducts as $index => $product) {
            // Calcular cuántos productos necesitaría vender para alcanzar la meta
            $profitPerUnit = $product->selling_price - $product->purchase_price;
            if ($profitPerUnit <= 0) continue; // Evitar división por cero o productos sin ganancia
            
            $unitsNeeded = ceil($remainingAmount / $profitPerUnit);
            
            // Limitar a un valor razonable basado en inventario y demanda histórica
            $adjustedUnits = min($unitsNeeded, max(10, $product->stock * 0.5));
            
            $recommendations[] = [
                'sales_goal_id' => $goal->id,
                'product_id' => $product->id,
                'recommended_quantity' => (int)$adjustedUnits,
                'expected_revenue' => $adjustedUnits * $product->selling_price,
                'priority' => count($prioritizedProducts) - $index // Prioridad inversa al índice
            ];
        }
        
        // Guardar recomendaciones
        $goal->productRecommendations()->createMany($recommendations);
    }
    
    /**
     * Genera recomendaciones de combos
     */
    protected function generateComboRecommendations(SalesGoal $goal, $profitableProducts)
    {
        // Crear combos inteligentes basados en productos complementarios
        $combos = $this->createOptimalCombos($profitableProducts);
        
        foreach ($combos as $index => $combo) {
            // Crear combo
            $comboRec = $goal->comboRecommendations()->create([
                'combo_name' => $combo['name'],
                'combo_description' => $combo['description'],
                'combo_price' => $combo['price'],
                'expected_profit' => $combo['profit'],
                'priority' => count($combos) - $index // Prioridad inversa al índice
            ]);
            
            // Agregar productos al combo
            foreach ($combo['products'] as $product) {
                $comboRec->products()->create([
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity']
                ]);
            }
        }
    }
    
    /**
     * Prioriza productos basados en rentabilidad y popularidad
     */
    protected function prioritizeProducts($profitableProducts, $bestSellingProducts)
    {
        // Crear un array para puntuación
        $scoredProducts = [];
        
        // Asignar puntuación por rentabilidad (máximo 10 puntos)
        foreach ($profitableProducts as $index => $product) {
            $scoredProducts[$product->id] = [
                'product' => $product,
                'profit_score' => max(0, 10 - ($index * 0.5)),
                'popularity_score' => 0
            ];
        }
        
        // Asignar puntuación por popularidad (máximo 10 puntos)
        foreach ($bestSellingProducts as $index => $product) {
            if (isset($scoredProducts[$product->id])) {
                $scoredProducts[$product->id]['popularity_score'] = max(0, 10 - ($index * 0.5));
            } else {
                $scoredProducts[$product->id] = [
                    'product' => $product,
                    'profit_score' => 0,
                    'popularity_score' => max(0, 10 - ($index * 0.5))
                ];
            }
        }
        
        // Calcular puntuación total y ordenar
        $prioritizedProducts = [];
        foreach ($scoredProducts as $id => $data) {
            // Ponderación: rentabilidad 60%, popularidad 40%
            $totalScore = ($data['profit_score'] * 0.6) + ($data['popularity_score'] * 0.4);
            $data['total_score'] = $totalScore;
            $prioritizedProducts[] = $data;
        }
        
        // Ordenar por puntuación total (descendente)
        usort($prioritizedProducts, function($a, $b) {
            return $b['total_score'] <=> $a['total_score'];
        });
        
        // Extraer solo los productos
        return array_map(function($item) {
            return $item['product'];
        }, $prioritizedProducts);
    }
    
    /**
     * Crea combos óptimos basados en análisis de datos
     */
    protected function createOptimalCombos($products)
    {
        $combos = [];
        
        // Implementación simplificada: crear combos con los 3 productos más rentables
        $topProducts = $products->take(6);
        
        // Combo 1: Los 3 productos más rentables
        if ($topProducts->count() >= 3) {
            $combo1Products = $topProducts->take(3);
            $price = 0;
            $cost = 0;
            $comboProducts = [];
            
            foreach ($combo1Products as $product) {
                $price += $product->selling_price;
                $cost += $product->purchase_price;
                $comboProducts[] = [
                    'id' => $product->id,
                    'quantity' => 1
                ];
            }
            
            // Aplicar un pequeño descuento (5%)
            $discountedPrice = $price * 0.95;
            
            $combos[] = [
                'name' => 'Combo Premium',
                'description' => 'Combinación de nuestros 3 productos más rentables con un 5% de descuento',
                'price' => $discountedPrice,
                'profit' => $discountedPrice - $cost,
                'products' => $comboProducts
            ];
        }
        
        // Combo 2: Producto caro + 2 productos complementarios
        if ($topProducts->count() >= 3) {
            $mainProduct = $topProducts->first();
            $complementaryProducts = $topProducts->slice(3, 2);
            
            if ($complementaryProducts->count() >= 2) {
                $price = $mainProduct->selling_price;
                $cost = $mainProduct->purchase_price;
                $comboProducts = [
                    [
                        'id' => $mainProduct->id,
                        'quantity' => 1
                    ]
                ];
                
                foreach ($complementaryProducts as $product) {
                    $price += $product->selling_price;
                    $cost += $product->purchase_price;
                    $comboProducts[] = [
                        'id' => $product->id,
                        'quantity' => 1
                    ];
                }
                
                // Aplicar un pequeño descuento (8%)
                $discountedPrice = $price * 0.92;
                
                $combos[] = [
                    'name' => 'Combo Valor',
                    'description' => 'Nuestro mejor producto con complementos ideales con un 8% de descuento',
                    'price' => $discountedPrice,
                    'profit' => $discountedPrice - $cost,
                    'products' => $comboProducts
                ];
            }
        }
        
        // Combo 3: Combo de volumen (2 unidades del producto más vendido)
        if ($topProducts->count() >= 1) {
            $volumeProduct = $topProducts->first();
            $price = $volumeProduct->selling_price * 2;
            $cost = $volumeProduct->purchase_price * 2;
            
            // Aplicar un descuento por volumen (10%)
            $discountedPrice = $price * 0.9;
            
            $combos[] = [
                'name' => 'Combo Doble',
                'description' => 'Lleva 2 unidades de nuestro producto estrella con un 10% de descuento',
                'price' => $discountedPrice,
                'profit' => $discountedPrice - $cost,
                'products' => [
                    [
                        'id' => $volumeProduct->id,
                        'quantity' => 2
                    ]
                ]
            ];
        }
        
        return $combos;
    }
    
    /**
     * Actualiza el progreso de una meta basado en ventas y gastos
     */
    public function updateGoalProgress(SalesGoal $goal)
    {
        // Calcular ventas en el período
        $sales = Sale::whereBetween('created_at', [$goal->start_date, $goal->end_date])
                    ->sum('total'); // Corrigiendo el nombre de la columna (es 'total', no 'total_amount')
        
        // También considerar ingresos adicionales del sistema contable
        $otherIncome = AccountingTransaction::where('type', 'income')
                        ->whereNull('sale_id') // Solo los que no están asociados a ventas para evitar duplicados
                        ->whereBetween('created_at', [$goal->start_date, $goal->end_date])
                        ->sum('amount');
        
        // Total de ingresos (ventas + otros ingresos)
        $totalIncome = $sales + $otherIncome;
        
        // Si se deben descontar gastos
        if ($goal->deduct_expenses) {
            $expenses = AccountingTransaction::where('type', 'expense')
                        ->whereBetween('created_at', [$goal->start_date, $goal->end_date])
                        ->sum('amount');
            
            $currentAmount = $totalIncome - $expenses;
        } else {
            $currentAmount = $totalIncome;
        }
        
        // Actualizar progreso (permitiendo valores negativos para mostrar el impacto real de los gastos)
        $goal->current_amount = $currentAmount;
        
        // Actualizar estado si es necesario
        if ($goal->current_amount >= $goal->target_amount) {
            $goal->status = 'completed';
        } elseif ($goal->end_date < now() && $goal->status == 'active') {
            $goal->status = 'failed';
        }
        
        $goal->save();
        
        return $goal;
    }
}
