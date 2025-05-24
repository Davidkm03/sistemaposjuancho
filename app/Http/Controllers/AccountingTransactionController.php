<?php

namespace App\Http\Controllers;

use App\Models\AccountingTransaction;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountingTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AccountingTransaction::with(['sale', 'user']);
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Filter by reference
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('reference', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }
        
        // Sort results
        $sortField = $request->sort_by ?? 'transaction_date';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $transactions = $query->paginate(15);
        
        // Get transaction types for filter
        $types = AccountingTransaction::select('type')->distinct()->pluck('type');
        
        // Calculate summary statistics
        $totals = [
            'income' => AccountingTransaction::where('type', 'income')->sum('amount'),
            'expense' => AccountingTransaction::where('type', 'expense')->sum('amount'),
            'sale' => AccountingTransaction::where('type', 'sale')->sum('amount'),
            'purchase' => AccountingTransaction::where('type', 'purchase')->sum('amount'),
            'adjustment' => AccountingTransaction::where('type', 'adjustment')->sum('amount'),
            'balance' => AccountingTransaction::sum('amount'),
        ];
        
        return view('accounting.index', compact('transactions', 'types', 'totals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get sales for reference
        $sales = Sale::orderBy('created_at', 'desc')->limit(20)->get();
        
        return view('accounting.create', compact('sales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense,purchase,adjustment',
            'reference' => 'required|string|max:100',
            'amount' => 'required|numeric',
            'sale_id' => 'nullable|exists:sales,id',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'transaction_date' => 'required|date',
        ]);
        
        try {
            AccountingTransaction::create([
                'type' => $request->type,
                'reference' => $request->reference,
                'amount' => $request->type == 'expense' || $request->type == 'purchase' 
                            ? -abs($request->amount) // Make amount negative for expenses
                            : $request->amount,
                'sale_id' => $request->sale_id,
                'user_id' => auth()->id(),
                'description' => $request->description,
                'category' => $request->category,
                'transaction_date' => $request->transaction_date,
            ]);
            
            return redirect()->route('accounting.index')
                             ->with('success', 'Transaction created successfully');
            
        } catch (Exception $e) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Error creating transaction: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AccountingTransaction $accounting)
    {
        $accounting->load(['sale', 'user']);
        return view('accounting.show', compact('accounting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccountingTransaction $accounting)
    {
        // Don't allow editing of sale transactions
        if ($accounting->type === 'sale') {
            return redirect()->route('accounting.show', $accounting)
                             ->with('error', 'Sale transactions cannot be edited');
        }
        
        $sales = Sale::orderBy('created_at', 'desc')->limit(20)->get();
        return view('accounting.edit', compact('accounting', 'sales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccountingTransaction $accounting)
    {
        // Don't allow updating of sale transactions
        if ($accounting->type === 'sale') {
            return redirect()->route('accounting.show', $accounting)
                             ->with('error', 'Sale transactions cannot be updated');
        }
        
        $request->validate([
            'reference' => 'required|string|max:100',
            'amount' => 'required|numeric',
            'sale_id' => 'nullable|exists:sales,id',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'transaction_date' => 'required|date',
        ]);
        
        try {
            $accounting->update([
                'reference' => $request->reference,
                'amount' => $accounting->type == 'expense' || $accounting->type == 'purchase' 
                          ? -abs($request->amount) // Make amount negative for expenses
                          : $request->amount,
                'sale_id' => $request->sale_id,
                'description' => $request->description,
                'category' => $request->category,
                'transaction_date' => $request->transaction_date,
            ]);
            
            return redirect()->route('accounting.show', $accounting)
                             ->with('success', 'Transaction updated successfully');
            
        } catch (Exception $e) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Error updating transaction: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountingTransaction $accounting)
    {
        // Don't allow deletion of sale transactions
        if ($accounting->type === 'sale') {
            return redirect()->route('accounting.index')
                             ->with('error', 'Sale transactions cannot be deleted directly');
        }
        
        $accounting->delete();
        
        return redirect()->route('accounting.index')
                         ->with('success', 'Transaction deleted successfully');
    }
    
    /**
     * Generate financial reports
     */
    public function reports(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        
        // Income statement (profit & loss)
        $incomeStatement = [
            'income' => AccountingTransaction::where('type', 'income')
                        ->whereDate('transaction_date', '>=', $startDate)
                        ->whereDate('transaction_date', '<=', $endDate)
                        ->sum('amount'),
                        
            'expenses' => AccountingTransaction::where('type', 'expense')
                        ->whereDate('transaction_date', '>=', $startDate)
                        ->whereDate('transaction_date', '<=', $endDate)
                        ->sum('amount'),
                        
            'adjustments' => AccountingTransaction::where('type', 'adjustment')
                        ->whereDate('transaction_date', '>=', $startDate)
                        ->whereDate('transaction_date', '<=', $endDate)
                        ->sum('amount'),
        ];
        
        $incomeStatement['profit'] = $incomeStatement['income'] - $incomeStatement['expenses'] + $incomeStatement['adjustments'];
        
        // Monthly revenue for chart - usando funciones compatibles con SQLite
        $monthlyRevenue = DB::table('accounting_transactions')
            ->select(
                DB::raw("strftime('%Y', transaction_date) as year"),
                DB::raw("strftime('%m', transaction_date) as month"),
                DB::raw('SUM(amount) as total')
            )
            ->where('type', 'income')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => date('M Y', mktime(0, 0, 0, (int)$item->month, 1, (int)$item->year)),
                    'total' => $item->total
                ];
            });
        
        // Category breakdown
        $categoryBreakdown = DB::table('accounting_transactions')
            ->select('category', DB::raw('SUM(amount) as total'), 'type')
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->whereNotNull('category')
            ->groupBy('category', 'type')
            ->orderBy('total', 'desc')
            ->get();
        
        return view('accounting.reports', compact(
            'startDate', 
            'endDate', 
            'incomeStatement', 
            'monthlyRevenue',
            'categoryBreakdown'
        ));
    }
}
