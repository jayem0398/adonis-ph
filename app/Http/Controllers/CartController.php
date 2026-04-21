<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the persistent cart archive.
     */
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with(['variant.product'])
            ->get();

        $itemsMapped = $cartItems->map(function ($item) {
            return [
                'id' => $item->id,
                'variant_id' => $item->product_variant_id,
                'name' => $item->variant->product->name,
                'size' => $item->variant->size,
                'price' => $item->variant->price,
                'quantity' => $item->quantity,
                'image' => $item->variant->product->image_path,
                'category' => $item->variant->product->category,
                'stock' => $item->variant->stock,
            ];
        });

        $total = $cartItems->sum(function ($item) {
            return $item->variant->price * $item->quantity;
        });

        return Inertia::render('Cart/Index', [
            'cartItems' => $itemsMapped,
            'cartTotal' => (float)$total
        ]);
    }

    /**
     * Sync unit configuration to database archive.
     */
    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);
        
        if ($variant->stock <= 0) {
            return redirect()->back()->with('error', 'UNIT_UNAVAILABLE_STOCK_DEPLETED');
        }

        $quantityToAdd = $request->quantity ?? 1;

        $existingItem = Cart::where('user_id', Auth::id())
            ->where('product_variant_id', $variant->id)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantityToAdd);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_variant_id' => $variant->id,
                'quantity' => $quantityToAdd,
            ]);
        }

        if ($request->redirect_to_checkout) {
            return redirect()->route('checkout.index');
        }

        return redirect()->back()->with('success', 'UNIT_SYNCED_TO_DATABASE_ARCHIVE');
    }

    /**
     * Update unit quantity in persistent storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);
        $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);

        if ($request->quantity <= 0) {
            $cartItem->delete();
        } else {
            $cartItem->update(['quantity' => $request->quantity]);
        }

        return redirect()->back()->with('success', 'ARCHIVE_QUANTITY_UPDATED');
    }

    /**
     * Remove record from database archive.
     */
    public function destroy($id)
    {
        Cart::where('user_id', Auth::id())->findOrFail($id)->delete();
        return redirect()->back()->with('success', 'RECORD_REMOVED_FROM_DATABASE');
    }
}