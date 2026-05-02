<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // 1. Kunin ang 'ids' mula sa request
        $idsParam = $request->query('ids', '');
        
        $selectedIds = [];

        // 2. SIGURADUHIN na magiging Array ito bago ipasa sa query
        if (is_string($idsParam) && $idsParam !== '') {
            // Kung "1,2,3" ang format, gagawin nating [1, 2, 3]
            $selectedIds = explode(',', $idsParam);
        } elseif (is_array($idsParam)) {
            $selectedIds = $idsParam;
        }

        $query = Cart::where('user_id', Auth::id())->with(['variant.product']);

        // 3. Dito na natin gagamitin ang array. Hindi na ito mag-eerror sa Builder.php
        if (!empty($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        }

        $cartItems = $query->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'No valid items found for checkout.');
        }

        $cartMapped = $cartItems->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->variant->product->name,
                'size' => $item->variant->size,
                'price' => (float)$item->variant->price,
                'quantity' => $item->quantity,
                'image' => $item->variant->product->image_path,
            ];
        });

        return Inertia::render('Checkout/Index', [
            'cart' => $cartMapped,
            'auth' => ['user' => Auth::user()]
        ]);
    }

    public function validateVoucher(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $code = strtoupper(trim($request->code));
        $voucher = Voucher::where('code', $code)->where('is_active', true)->first();

        if (!$voucher) {
            return back()->with('error_voucher', 'Invalid voucher code.');
        }

        if ($voucher->usage_limit > 0 && $voucher->used_count >= $voucher->usage_limit) {
            return back()->with('error_voucher', 'Voucher usage limit reached.');
        }

        if ($voucher->expires_at && Carbon::parse($voucher->expires_at)->isPast()) {
            return back()->with('error_voucher', 'Voucher has expired.');
        }

        return back()->with('success_voucher', [
            'code' => $voucher->code,
            'type' => $voucher->type,
            'value' => (float)$voucher->value,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'payment_method' => 'required|string',
            'voucher_code' => 'nullable|string',
            'cart_ids' => 'required|array'
        ]);

        $cartItems = Cart::whereIn('id', $request->cart_ids)
            ->where('user_id', Auth::id())
            ->with(['variant.product'])
            ->get();

        if ($cartItems->isEmpty()) return redirect()->route('welcome');

        $subtotal = $cartItems->sum(fn($item) => $item->variant->price * $item->quantity);
        $discount = 0;
        $activeVoucherModel = null;

        if ($request->voucher_code) {
            $activeVoucherModel = Voucher::where('code', strtoupper($request->voucher_code))
                ->where('is_active', true)
                ->first();
                
            if ($activeVoucherModel) {
                $discount = ($activeVoucherModel->type === 'percentage') 
                    ? $subtotal * ($activeVoucherModel->value / 100) 
                    : $activeVoucherModel->value;
            }
        }

        $finalTotal = max(0, $subtotal - $discount);

        try {
            $order = DB::transaction(function () use ($request, $cartItems, $finalTotal, $activeVoucherModel) {
                foreach ($cartItems as $item) {
                    if ($item->variant->stock < $item->quantity) {
                        throw new \Exception("Insufficient stock for {$item->variant->product->name}");
                    }
                }

                $newOrder = Order::create([
                    'user_id' => Auth::id(),
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'city' => $request->city,
                    'payment_method' => $request->payment_method,
                    'total_amount' => $finalTotal,
                    'status' => 'pending',
                    'voucher_code' => $request->voucher_code ? strtoupper($request->voucher_code) : null,
                    'fulfillment_status' => 'unfulfilled'
                ]);

                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $newOrder->id,
                        'product_name' => $item->variant->product->name,
                        'variant_name' => $item->variant->size,
                        'quantity' => $item->quantity,
                        'price' => $item->variant->price,
                    ]);
                    $item->variant->decrement('stock', $item->quantity);
                }

                if ($activeVoucherModel) {
                    $activeVoucherModel->increment('used_count');
                    if ($activeVoucherModel->usage_limit > 0 && $activeVoucherModel->used_count >= $activeVoucherModel->usage_limit) {
                        $activeVoucherModel->update(['is_active' => false]);
                    }
                }

                Cart::whereIn('id', $request->cart_ids)->where('user_id', Auth::id())->delete();
                
                return $newOrder;
            });

            return redirect()->route('checkout.success', $order->id);
        } catch (\Exception $e) {
            \Log::error('Checkout Failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Checkout Failed: ' . $e->getMessage()]);
        }
    }

    public function success($id)
    {
        $order = Order::with('items')->where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('OrderSuccess', ['order' => $order]);
    }
}