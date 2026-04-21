<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the Studio Operations Overview.
     * Synchronized with real-time archive data.
     */
    public function index()
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                // Revenue Metrics (Excluding cancelled orders)
                'total_sales' => (float) Order::where('status', '!=', 'cancelled')->sum('total_amount'),
                
                // Fulfillment Status
                'pending_orders' => Order::where('status', 'pending')->count(),
                
                // Inventory Summaries
                'total_stock' => (int) ProductVariant::sum('stock'),
                'low_stock_count' => ProductVariant::where('stock', '<', 5)->count(),
                
                // Recent Acquisitions Log (Latest 5 orders)
                'recent_orders' => Order::latest()
                    ->take(5)
                    ->get(['id', 'first_name', 'last_name', 'total_amount', 'status', 'created_at']),
                
                // Critical Stock Units (Details for the Warning section)
                'low_stock_items' => ProductVariant::with('product')
                    ->where('stock', '<', 5)
                    ->orderBy('stock', 'asc')
                    ->get(),

                // System Totals
                'total_products' => Product::count(),
                'items_sold' => (int) OrderItem::sum('quantity'),
            ]
        ]);
    }
}