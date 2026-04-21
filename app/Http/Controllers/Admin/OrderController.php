<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant; // Siguraduhing naka-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Orders', [
            'orders' => Order::with(['items.variant.product.images', 'user'])
                ->latest()
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'first_name' => $order->first_name,
                        'last_name' => $order->last_name,
                        'email' => $order->email,
                        'address' => $order->address,
                        'city' => $order->city,
                        'state' => $order->state,
                        'total_amount' => (float) $order->total_amount,
                        'status' => $order->status,
                        'items' => $order->items->map(function($item) {
                            $product = $item->variant?->product;
                            return [
                                'id' => $item->id,
                                'product_name' => $product?->name ?? $item->product_name,
                                'variant_name' => $item->variant?->size ?? $item->variant_name,
                                'price' => (float) $item->price,
                                'quantity' => $item->quantity,
                                'image' => $product?->images->first()?->image_path ?? null,
                            ];
                        }),
                        'return_proof_path' => $order->return_proof_path,
                        'return_reason' => $order->return_reason,
                        'return_description' => $order->return_description,
                    ];
                })
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        Order::findOrFail($id)->update(['status' => $request->status]);
        return redirect()->back()->with('success', "Status Updated.");
    }

    public function approveReturn($id)
    {
        DB::transaction(function () use ($id) {
            $order = Order::with('items')->findOrFail($id);
            
            // SELYADO: Re-stock Logic
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    ProductVariant::where('id', $item->product_variant_id)
                        ->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => 'refunded']);
        });

        return redirect()->back()->with('success', "Return Approved. Inventory Restocked.");
    }

    public function rejectReturn($id)
    {
        Order::findOrFail($id)->update(['status' => 'delivered']);
        return redirect()->back()->with('success', "Return Request Rejected.");
    }
}