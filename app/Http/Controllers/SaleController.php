<?php

namespace App\Http\Controllers;

use App\Models\AccountingTransaction;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SalesGoal;
use App\Services\GoalRecommendationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'user']);
        
        // Filter by invoice number
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('invoice_number', 'like', "%{$search}%");
        }
        
        // Filter by customer
        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Calculate total sales amount based on filters
        $totalSales = (clone $query)->sum('total');
        
        // Calculate completed sales count
        $completedSales = (clone $query)->where('status', 'completed')->count();
        
        // Calculate pending sales count
        $pendingSales = (clone $query)->where('status', 'pending')->count();
        
        // Calculate cancelled sales count
        $cancelledSales = (clone $query)->where('status', 'cancelled')->count();
        
        // Sort results
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $sales = $query->paginate(15);
        $customers = Customer::where('status', true)->get();
        
        return view('sales.index', compact('sales', 'customers', 'totalSales', 'completedSales', 'pendingSales', 'cancelledSales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::where('status', true)->get();
        $products = Product::where('status', true)
                           ->where('stock', '>', 0)
                           ->get();
        
        // Generate unique invoice number
        $latestSale = Sale::latest()->first();
        $invoiceNumber = 'INV-' . date('Ymd') . '-';
        
        if ($latestSale) {
            $lastNumber = substr($latestSale->invoice_number, -4);
            $invoiceNumber .= str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $invoiceNumber .= '0001';
        }
        
        return view('sales.create', compact('customers', 'products', 'invoiceNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar la petición
            $request->validate([
                'invoice_number' => 'required|string|unique:sales',
                'customer_id' => 'nullable|exists:customers,id',
                'products' => 'required|array|min:1',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.price' => 'required|numeric|min:0',
            ]);

            // Calcular totales
            $subtotal = 0;
            foreach ($request->products as $product) {
                $subtotal += $product['quantity'] * $product['price'];
            }

            // Calcular impuesto
            $taxRate = $request->input('tax_rate', 19); // Por defecto 19%
            $tax = ($subtotal * $taxRate) / 100;
            $total = $subtotal + $tax;

            // Crear la venta
            $sale = Sale::create([
                'invoice_number' => $request->invoice_number,
                'customer_id' => $request->customer_id,
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'tax_rate' => $taxRate,
                'total' => $total,
                'payment_method' => $request->input('payment_method', 'cash'),
                'status' => 'completed',
                'notes' => $request->input('notes'),
            ]);

            // Procesar detalles de la venta y actualizar inventario
            $productDetails = [];
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);
                
                // Crear detalle de venta
                $saleDetail = $sale->saleDetails()->create([
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);
                
                // Actualizar inventario del producto
                $product->update([
                    'stock' => $product->stock - $item['quantity'],
                ]);
                
                // Guardar detalles para la respuesta
                $productDetails[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price']
                ];
                $product->save();
            }
            
            // Create accounting transaction record
            AccountingTransaction::create([
                'type' => 'income',
                'reference' => $sale->invoice_number,
                'amount' => $sale->total,
                'sale_id' => $sale->id,
                'user_id' => auth()->id(),
                'description' => "Venta {$sale->invoice_number}",
                'category' => 'Ventas',
                'transaction_date' => now(),
            ]);
            
            // Update customer balance if applicable
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    // If customer has a balance, we could manage credits/debits here
                }
            }
            
            // Actualizar metas de ventas
            $this->updateSalesGoals();
            
            DB::commit();
            
            // Preparar respuesta JSON detallada
            $response = [
                'id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'subtotal' => $sale->subtotal,
                'tax' => $sale->tax,
                'tax_rate' => $sale->tax_rate,
                'total' => $sale->total,
                'payment_method' => $sale->payment_method,
                'status' => $sale->status,
                'created_at' => $sale->created_at->format('Y-m-d H:i:s'),
                'products' => $productDetails,
                'print_url' => url("/sales/{$sale->id}/print-invoice")
            ];
            
            return response()->json($response, 201);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'saleDetails.product']);
        
        // Obtener las transacciones contables asociadas a esta venta
        $transactions = \App\Models\AccountingTransaction::where('sale_id', $sale->id)
                            ->with('user')
                            ->orderBy('created_at', 'desc')
                            ->get();
        
        return view('sales.show', compact('sale', 'transactions'));
    }
    
    /**
     * Complete a sale by updating payment information
     */
    public function complete(Request $request, Sale $sale)
    {
        // Validar datos
        $request->validate([
            'payment_method' => 'required|in:cash,card,transfer',
            'payment_reference' => 'nullable|required_if:payment_method,card,transfer|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Actualizar información de pago
        $sale->payment_method = $request->payment_method;
        $sale->payment_reference = $request->payment_reference;
        $sale->notes = $request->notes;
        $sale->status = 'completed';
        $sale->save();
        
        // Actualizar todas las metas activas
        $this->updateSalesGoals();
        
        return redirect()->route('sales.show', $sale)
                        ->with('success', 'Pago completado correctamente.');
    }
    
    /**
     * Print the invoice for the specified sale
     */
    public function printInvoice(Sale $sale)
    {
        $sale->load(['customer', 'user', 'saleDetails.product']);
        return view('sales.invoice', compact('sale'));
    }
    
    /**
     * Actualiza las metas de ventas activas
     * 
     * @return void
     */
    protected function updateSalesGoals()
    {
        // Obtener todas las metas activas (asegurándonos de no usar caché)
        $activeGoals = SalesGoal::where('status', 'active')->get()->fresh();
        
        // Si no hay metas activas, no hacer nada
        if ($activeGoals->isEmpty()) {
            return;
        }
        
        // Usar el servicio para actualizar cada meta
        $recommendationService = app(GoalRecommendationService::class);
        
        foreach ($activeGoals as $goal) {
            // Actualizamos el progreso y guardamos inmediatamente
            $goal = $recommendationService->updateGoalProgress($goal);
            $goal->save();
        }
        
        // Registrar en el log que se actualizaron las metas
        \Log::info('Metas de ventas actualizadas después de una venta. Metas activas: ' . $activeGoals->count());
    }
    
    /**
     * Generate a PDF invoice for the specified sale
     */
    public function generateInvoice(Sale $sale)
    {
        $sale->load(['customer', 'user', 'saleDetails.product']);
        
        // Generate PDF logic here (using a package like DomPDF, TCPDF, etc.)
        // $pdf = PDF::loadView('sales.invoice', compact('sale'));
        // return $pdf->download("invoice-{$sale->invoice_number}.pdf");
        
        // For now just return the invoice view
        return view('sales.invoice', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        // Verificar si el usuario tiene permiso para editar ventas
        if (!auth()->user()->hasPermission('sales.edit')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para editar ventas.');
        }
        
        // Only allow editing of pending sales
        if ($sale->status !== 'pending') {
            return redirect()->route('sales.show', $sale)
                             ->with('error', 'Only pending sales can be edited');
        }
        
        $sale->load(['customer', 'saleDetails.product']);
        $customers = Customer::where('status', true)->get();
        $products = Product::where('status', true)->get();
        
        return view('sales.edit', compact('sale', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        // Verificar si el usuario tiene permiso para actualizar ventas
        if (!auth()->user()->hasPermission('sales.edit')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para actualizar ventas.');
        }
        
        // Only allow updating of pending sales
        if ($sale->status !== 'pending') {
            return redirect()->route('sales.show', $sale)
                             ->with('error', 'Only pending sales can be updated');
        }
        
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,card,transfer,other',
            'payment_reference' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:completed,pending,canceled',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update sale
            $sale->update([
                'customer_id' => $request->customer_id,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'notes' => $request->notes,
                'status' => $request->status,
            ]);
            
            // If sale is completed, finalize it
            if ($request->status === 'completed' && $sale->status !== 'completed') {
                // Create accounting transaction record
                AccountingTransaction::create([
                    'type' => 'income',
                    'reference' => $sale->invoice_number,
                    'amount' => $sale->total,
                    'sale_id' => $sale->id,
                    'user_id' => auth()->id(),
                    'description' => "Venta {$sale->invoice_number}",
                    'category' => 'Ventas',
                    'transaction_date' => now(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('sales.show', $sale)
                             ->with('success', 'Sale updated successfully');
            
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Error updating sale: ' . $e->getMessage());
        }
    }

    /**
     * Change the status of a sale to canceled
     */
    public function cancel(Sale $sale)
    {
        // Verificar si el usuario tiene permiso para cancelar ventas
        if (!auth()->user()->hasPermission('sales.cancel')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para cancelar ventas.');
        }
        
        // Only allow cancellation of pending or completed sales
        if ($sale->status === 'canceled') {
            return redirect()->route('sales.show', $sale)
                             ->with('error', 'Sale is already canceled');
        }
        
        DB::beginTransaction();
        
        try {
            // If sale was completed, restore product stock
            if ($sale->status === 'completed') {
                foreach ($sale->saleDetails as $detail) {
                    $product = $detail->product;
                    $product->stock += $detail->quantity;
                    $product->save();
                }
                
                // Create reversal accounting transaction
                AccountingTransaction::create([
                    'type' => 'expense',
                    'reference' => $sale->invoice_number . '-CANCEL',
                    'amount' => $sale->total, // Positive amount as expense (reversal)
                    'sale_id' => $sale->id,
                    'user_id' => auth()->id(),
                    'description' => "Cancelación de venta {$sale->invoice_number}",
                    'category' => 'Devoluciones',
                    'transaction_date' => now(),
                ]);
            }
            
            // Update sale status
            $sale->status = 'canceled';
            $sale->save();
            
            DB::commit();
            
            return redirect()->route('sales.show', $sale)
                             ->with('success', 'Sale canceled successfully');
            
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->with('error', 'Error canceling sale: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        // Verificar si el usuario tiene permiso para eliminar ventas
        if (!auth()->user()->hasPermission('sales.delete')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para eliminar ventas.');
        }
        
        // Only allow deletion of canceled sales
        if ($sale->status !== 'canceled') {
            return redirect()->route('sales.index')
                             ->with('error', 'Only canceled sales can be deleted');
        }
        
        // Delete accounting transactions
        $sale->accountingTransactions()->delete();
        
        // Delete sale details
        $sale->saleDetails()->delete();
        
        // Delete sale
        $sale->delete();
        
        return redirect()->route('sales.index')
                         ->with('success', 'Sale deleted successfully');
    }
    
    /**
     * View POS interface
     */
    public function pos()
    {
        $customers = Customer::where('status', true)->get();
        $products = Product::where('status', true)
                           ->where('stock', '>', 0)
                           ->get();
        
        // Generate unique invoice number
        $latestSale = Sale::latest()->first();
        $invoiceNumber = 'INV-' . date('Ymd') . '-';
        
        if ($latestSale) {
            $lastNumber = substr($latestSale->invoice_number, -4);
            $invoiceNumber .= str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $invoiceNumber .= '0001';
        }
        
        return view('sales.pos', compact('customers', 'products', 'invoiceNumber'));
    }
}
