<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        
        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->has('status')) {
            if ($request->status === '1' || $request->status === '0') {
                $query->where('status', $request->status);
            }
        }
        
        // Document type filter
        if ($request->has('document_type') && $request->document_type) {
            $query->where('document_type', $request->document_type);
        }
        
        // Sort results
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $customers = $query->paginate(10);
        
        // Document types for filter
        $documentTypes = [
            'DNI' => 'DNI',
            'RUC' => 'RUC',
            'PASAPORTE' => 'Pasaporte',
            'OTRO' => 'Otro'
        ];
        
        return view('customers.index', compact('customers', 'documentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $documentTypes = [
            'DNI' => 'DNI',
            'RUC' => 'RUC',
            'PASAPORTE' => 'Pasaporte',
            'OTRO' => 'Otro'
        ];
        
        return view('customers.create', compact('documentTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100|unique:customers',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'document_type' => 'nullable|string|max:20',
            'document_number' => 'nullable|string|max:20',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('customers.create')
                ->withErrors($validator)
                ->withInput();
        }

        Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'balance' => 0, // New customers start with zero balance
            'status' => $request->status ?? true,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        
        // Get recent sales for this customer
        $recentSales = Sale::where('customer_id', $id)
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
        
        // Get customer stats
        $totalPurchases = Sale::where('customer_id', $id)->sum('total');
        $purchaseCount = Sale::where('customer_id', $id)->count();
        
        // Payment history - using an empty collection for now
        // This will need to be implemented when a payment system is added
        $paymentHistory = collect([]);
        
        return view('customers.show', compact('customer', 'totalPurchases', 'purchaseCount', 'recentSales', 'paymentHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Verificar si el usuario tiene permiso para editar clientes
        if (!auth()->user()->hasPermission('customers.edit')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para editar clientes.');
        }
        
        $customer = Customer::findOrFail($id);
        
        $documentTypes = [
            'DNI' => 'DNI',
            'RUC' => 'RUC',
            'PASAPORTE' => 'Pasaporte',
            'OTRO' => 'Otro'
        ];
        
        return view('customers.edit', compact('customer', 'documentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Verificar si el usuario tiene permiso para actualizar clientes
        if (!auth()->user()->hasPermission('customers.edit')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para actualizar clientes.');
        }
        
        $customer = Customer::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100|unique:customers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'document_type' => 'nullable|string|max:20',
            'document_number' => 'nullable|string|max:20',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('customers.edit', $customer->id)
                ->withErrors($validator)
                ->withInput();
        }

        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'status' => $request->has('status') ? $request->status : $customer->status,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Verificar si el usuario tiene permiso para eliminar clientes
        if (!auth()->user()->hasPermission('customers.delete')) {
            return redirect()->route('dashboard')
                             ->with('error', 'No tienes permiso para eliminar clientes.');
        }
        
        $customer = Customer::findOrFail($id);
        
        // Check if customer has sales
        $salesCount = Sale::where('customer_id', $id)->count();
        
        if ($salesCount > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'No se puede eliminar el cliente porque tiene ventas asociadas. Desactive el cliente en su lugar.');
        }
        
        $customer->delete();
        
        return redirect()->route('customers.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }



}
