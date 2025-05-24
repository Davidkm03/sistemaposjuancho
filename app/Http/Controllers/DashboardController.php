<?php

namespace App\Http\Controllers;

use App\Models\AccountingTransaction;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display dashboard with important business metrics
     */
    public function index()
    {
        // Get today's date and last 30 days for reports
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth();
        
        // Sales metrics
        $todaySales = Sale::whereDate('created_at', $today)->sum('total');
        $monthSales = Sale::whereMonth('created_at', $today->month)
                          ->whereYear('created_at', $today->year)
                          ->sum('total');
        $lastMonthSales = Sale::whereMonth('created_at', $lastMonth->month)
                              ->whereYear('created_at', $lastMonth->year)
                              ->sum('total');
        
        // Calculate growth percentage
        $salesGrowth = $lastMonthSales > 0 
            ? (($monthSales - $lastMonthSales) / $lastMonthSales) * 100 
            : 100;
            
        // Count totals
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalSales = Sale::count();
        
        // Products low on stock
        $lowStockProducts = Product::whereRaw('stock <= min_stock')->get();
        
        // Get recent sales
        $recentSales = Sale::with(['customer', 'user'])
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();
        
        // Get top selling products
        $topSellingProducts = SaleDetail::select('product_id', DB::raw('SUM(quantity) as total_sold'))
                                      ->groupBy('product_id')
                                      ->orderBy('total_sold', 'desc')
                                      ->limit(5)
                                      ->with('product')
                                      ->get();
        
        // Get sales by day for the current month (for chart)
        $salesByDay = Sale::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
                          ->whereMonth('created_at', $today->month)
                          ->whereYear('created_at', $today->year)
                          ->groupBy('date')
                          ->orderBy('date')
                          ->get()
                          ->map(function ($item) {
                              return [
                                  'date' => Carbon::parse($item->date)->format('d-M'),
                                  'total' => $item->total
                              ];
                          });
        
        return view('dashboard', compact(
            'todaySales',
            'monthSales',
            'salesGrowth',
            'totalProducts',
            'totalCustomers', 
            'totalSales',
            'lowStockProducts',
            'recentSales',
            'topSellingProducts',
            'salesByDay'
        ));
    }
}
