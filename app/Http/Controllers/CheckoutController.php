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
        // KUNIN ANG IDS MULA SA URL QUERY (?ids[]=1&ids[]=2)
        $selectedIds = $request->query('ids', []);

        $query = Cart::where('user_id', Auth::id())->with(['variant.product']);

        // SELYADO: Kung may sinend na IDs, i-filter lang yung mga yun
        if (!empty($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        }

        $cartItems = $query->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Archive Error: No units selected for settlement.');
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

        return Inertia::render('Checkout', [
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
            return back()->with('error_voucher', 'Protocol Error: Code unrecognized.');
        }

        if ($voucher->usage_limit > 0 && $voucher->used_count >= $voucher->usage_limit) {
            return back()->with('error_voucher', 'Protocol Error: Voucher Depleted.');
        }

        if ($voucher->expires_at && Carbon::parse($voucher->expires_at)->isPast()) {
            return back()->with('error_voucher', 'Protocol Error: Code Expired.');
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
            'cart_ids' => 'required|array' // Idagdag natin ito para alam kung ano lang ang idedelete
        ]);

        // Kunin lang yung specific cart items na binili
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
                        throw new \Exception("Stock error for {$item->variant->product->name}");
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

                // SELYADO: Delete lang yung cart items na kasama sa order na ito
                Cart::whereIn('id', $request->cart_ids)->where('user_id', Auth::id())->delete();
                
                return $newOrder;
            });

            return redirect()->route('checkout.success', $order->id);
        } catch (\Exception $e) {
            \Log::error('Checkout Failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'System Error: ' . $e->getMessage()]);
        }
    }

    public function success($id)
    {
        $order = Order::with('items')->where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('OrderSuccess', ['order' => $order]);
    }
}