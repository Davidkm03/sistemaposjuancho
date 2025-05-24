<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();
        
        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('tax_number', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->has('status')) {
            if ($request->status === '1' || $request->status === '0') {
                $query->where('status', $request->status);
            }
        }
        
        // Sort results
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $suppliers = $query->withCount('products')->paginate(10);
        
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100|unique:suppliers',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('suppliers.create')
                ->withErrors($validator)
                ->withInput();
        }

        Supplier::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'contact_person' => $request->contact_person,
            'tax_number' => $request->tax_number,
            'status' => $request->status ?? true,
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        
        // Obtener productos paginados del proveedor en lugar de cargar la relación
        $products = Product::where('supplier_id', $id)
                    ->where('status', true)
                    ->orderBy('name')
                    ->paginate(10);
        
        // Calcular estadísticas para el proveedor
        $totalProducts = Product::where('supplier_id', $id)
                        ->where('status', true)
                        ->count();
                        
        // En un sistema real, esto podría venir de una tabla de compras o pedidos
        // Por ahora, usamos un cálculo simple basado en los productos
        $totalPurchases = Product::where('supplier_id', $id)
                         ->where('status', true)
                         ->sum('purchase_price');
        
        // También sería útil tener las compras recientes
        $recentPurchases = collect([]); // Colección vacía por ahora hasta implementar compras
        
        return view('suppliers.show', compact('supplier', 'products', 'totalProducts', 'totalPurchases', 'recentPurchases'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100|unique:suppliers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('suppliers.edit', $supplier->id)
                ->withErrors($validator)
                ->withInput();
        }

        $supplier->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'contact_person' => $request->contact_person,
            'tax_number' => $request->tax_number,
            'status' => $request->has('status') ? $request->status : $supplier->status,
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        
        // Check if supplier has products
        $productsCount = Product::where('supplier_id', $id)->count();
        
        if ($productsCount > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', 'No se puede eliminar el proveedor porque tiene productos asociados. Desactive el proveedor en su lugar.');
        }
        
        $supplier->delete();
        
        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }



}
