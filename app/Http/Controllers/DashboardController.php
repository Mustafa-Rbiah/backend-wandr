<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index() {
        $calculateTrend = function ($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100 : 0;
            return round((($current - $previous) / $previous) * 100, 1);
        };
    
        // --- حسابات Trends (نفس اللي درنا قبل) ---
        $thisMonthRevenue = \App\Models\Order::where('status', 'processing')->whereMonth('created_at', now()->month)->sum('total_price');
        $lastMonthRevenue = \App\Models\Order::where('status', 'processing')->whereMonth('created_at', now()->subMonth()->month)->sum('total_price');
        $revenueTrend = $calculateTrend($thisMonthRevenue, $lastMonthRevenue);
    
        $thisMonthOrders = \App\Models\Order::whereMonth('created_at', now()->month)->count();
        $lastMonthOrders = \App\Models\Order::whereMonth('created_at', now()->subMonth()->month)->count();
        $ordersTrend = $calculateTrend($thisMonthOrders, $lastMonthOrders);
    
        $thisMonthUsers = \App\Models\User::where('is_admin', false)->whereMonth('created_at', now()->month)->count();
        $lastMonthUsers = \App\Models\User::where('is_admin', false)->whereMonth('created_at', now()->subMonth()->month)->count();
        $customersTrend = $calculateTrend($thisMonthUsers, $lastMonthUsers);
    
        $overallGrowth = round(($revenueTrend + $ordersTrend + $customersTrend) / 3, 1);
        
        $totalProductsCount = \App\Models\Product::count();


        $inventoryMix = \App\Models\Product::withCount(['orderItems as total_sold' => function($query) {
            $query->select(DB::raw('sum(quantity)'));
        }])
        ->orderByDesc('total_sold') 
        ->get()
        ->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'image' => str_starts_with($product->image, 'http') ? $product->image : url('storage/' . $product->image),
                'total_sold' => (int) $product->total_sold ?: 0,
                'price' => $product->price
            ];
        });
        $grandTotalSold = $inventoryMix->sum('total_sold');

        
    
        return response()->json([
            'success' => true,
            'stats' => [
                'total_revenue' => ['value' => (float) \App\Models\Order::where('status', 'processing')->sum('total_price')],
                'total_orders'  => ['value' => \App\Models\Order::count()],
                'total_customers' => ['value' => \App\Models\User::where('is_admin', false)->count()],
                'overall_growth' => ['value' => $overallGrowth], 
                'total_products_count' => $totalProductsCount,
            ],
            'inventory_mix' => $inventoryMix,
            'grand_total_sold' => $grandTotalSold
        ]);
    }

    public function getSalesChart(Request $request) {
    $period = $request->query('period', '7d'); // الافتراضي هو 7 أيام
    $data = [];
    $format = '';

    switch ($period) {
        case '24h':
            $start = now()->subHours(24);
            $data = Order::where('created_at', '>=', $start)
                ->select(DB::raw('HOUR(created_at) as label'), DB::raw('count(*) as total'))
                ->groupBy('label')->orderBy('label')->get();
            break;
        case '7d':
            $start = now()->subDays(7);
            $data = Order::where('created_at', '>=', $start)
                ->select(DB::raw('DATE_FORMAT(created_at, "%d %b") as label'), DB::raw('count(*) as total'))
                ->groupBy('label')->get();
            break;
        case '1m':
            $start = now()->subMonth();
            $data = Order::where('created_at', '>=', $start)
                ->select(DB::raw('DATE_FORMAT(created_at, "%d %b") as label'), DB::raw('count(*) as total'))
                ->groupBy('label')->get();
            break;
        case '12m':
        case '6m':
            $months = ($period === '12m') ? 12 : 6;
            $start = now()->subMonths($months);
            $data = Order::where('created_at', '>=', $start)
                ->select(DB::raw('DATE_FORMAT(created_at, "%b %Y") as label'), DB::raw('count(*) as total'))
                ->groupBy('label')->get();
            break;
        case 'all':
            $data = Order::select(DB::raw('DATE_FORMAT(created_at, "%Y") as label'), DB::raw('count(*) as total'))
                ->groupBy('label')->get();
            break;
    }

    return response()->json($data);
}

}