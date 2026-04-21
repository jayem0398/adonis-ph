<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class FulfillmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Fulfillment/Index', [
            'orders' => Order::with(['user', 'items.product', 'items.variant'])
                ->latest()
                ->get()
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'tracking_number' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request, $order) {
            $oldStatus = $order->fulfillment_status;
            $newStatus = $request->status;

            // INVENTORY LOGIC: Kung i-ka-cancel ang order, ibalik ang stock
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    $item->variant->increment('stock', $item->quantity);
                }
            }

            $order->update([
                'fulfillment_status' => $newStatus,
                'tracking_number' => $request->tracking_number ?? $order->tracking_number,
                'shipped_at' => $newStatus === 'shipped' ? now() : $order->shipped_at
            ]);

            return redirect()->back()->with('success', "Order #{$order->id} updated to {$newStatus}.");
        });
    }
}