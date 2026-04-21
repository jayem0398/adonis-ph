<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items.product']) 
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'total_amount' => (float) $order->total_amount,
                    'status' => $order->status,
                    'first_name' => $order->first_name,
                    'last_name' => $order->last_name,
                    'created_at' => $order->created_at->format('M d, Y'),
                    'items' => $order->items 
                ];
            });

        return Inertia::render('Orders/Index', [
            'orders' => $orders
        ]);
    }

    public function show($id)
    {
        $order = Order::where('user_id', auth()->id())
            ->with(['items.product'])
            ->findOrFail($id);

        return Inertia::render('Orders/Show', [
            'order' => $order
        ]);
    }

    /**
     * Handle Order Cancellation
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('status', 'pending') // Double check status
            ->findOrFail($id);

        $request->validate([
            'reason_type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $order->update([
            'status' => 'cancelled',
            'cancel_reason' => $request->reason_type,
            'cancel_description' => $request->description,
            'cancelled_at' => now(),
        ]);

        // Protocol: Ibalik ang stocks sa inventory
        foreach ($order->items as $item) {
            if ($item->variant) {
                $item->variant->increment('stock', $item->quantity);
            }
        }

        return back()->with('success', 'Transaction cancelled. Stocks have been restored to repository.');
    }

    /**
     * Handle Return/Refund Request
     */
    public function returnRequest(Request $request, $id)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('status', 'delivered') // Only delivered can be returned
            ->findOrFail($id);

        $request->validate([
            'reason_type' => 'required|string',
            'description' => 'nullable|string',
            'proof' => 'required|file|mimes:jpg,jpeg,png,mp4,mov|max:20480', // Max 20MB
        ]);

        // Store proof file in public/returns folder
        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('returns', 'public');
        }

        $order->update([
            'status' => 'return_requested',
            'return_reason' => $request->reason_type,
            'return_description' => $request->description,
            'return_proof_path' => $proofPath,
        ]);

        return back()->with('success', 'Return request submitted. Awaiting studio verification.');
    }
}