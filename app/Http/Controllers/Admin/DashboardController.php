<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->input('range', '7days');

        // Global Intelligence Metrics
        $stats = [
            'total_revenue' => (float) Order::where('status', 'delivered')->sum('total_amount') ?? 0,
            'total_orders' => (int) Order::count() ?? 0,
            'total_units' => (int) Product::count() ?? 0,
            'low_stock' => (int) ProductVariant::where('stock', '<', 5)->count() ?? 0,
            'active_vouchers' => (int) Voucher::where('is_active', true)->count() ?? 0,
        ];

        // REVENUE MANIFEST: Filtered by Date Range
        $query = Order::where('status', 'delivered');

        switch ($range) {
            case 'this_month':
                $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                $dateFormat = 'M d';
                break;
            case 'last_month':
                $query->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);
                $dateFormat = 'M d';
                break;
            case 'this_year':
                $query->whereYear('created_at', now()->year);
                $dateFormat = 'M';
                break;
            case '7days':
            default:
                $query->where('created_at', '>=', now()->subDays(6));
                $dateFormat = 'D';
                break;
        }

        $chart_data = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as amount')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(fn($data) => [
                'date' => date($dateFormat, strtotime($data->date)),
                'amount' => (float) $data->amount
            ]);

        // Recent Manifest Logs with Identity Sync
        $recent_activity = Order::latest()
            ->take(8)
            ->get()
            ->map(fn($order) => [
                'id' => $order->id,
                'user_name' => $order->first_name . ' ' . $order->last_name,
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'created_at' => $order->created_at,
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recent_activity' => $recent_activity,
            'chart_data' => $chart_data,
            'current_range' => $range
        ]);
    }
}