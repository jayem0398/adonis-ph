<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController; 
use App\Http\Controllers\ArchiveController; 

// ADMIN COMMAND CENTER CONTROLLERS
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController; 
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request; 
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| ADONIS PH | Studio Archive Routes // System 2026
|--------------------------------------------------------------------------
*/

/**
 * Public Gallery & Shop Front
 */
Route::get('/', [ProductController::class, 'index'])->name('welcome');
Route::get('/archive', [ArchiveController::class, 'index'])->name('archive.index');
Route::get('/archive/{id}', [ProductController::class, 'show'])->name('products.show');

/*
|--------------------------------------------------------------------------
| Protected Customer Routes (Auth Required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // CART PROTOCOLS
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    // ACQUISITION & CHECKOUT PROTOCOLS
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{id}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/validate-voucher', [CheckoutController::class, 'validateVoucher'])->name('checkout.validate_voucher');

    // ORDER HISTORY & MODIFICATION MANIFEST
    Route::get('/my-orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/my-orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    
    /**
     * SELYADO FIX: Cancellation & Return Protocols
     */
    Route::post('/my-orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/my-orders/{order}/return', [OrderController::class, 'returnRequest'])->name('orders.return');

    // USER IDENTITY SYNC
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Protected Studio Administration (Auth & Admin Required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {
    
    // SYSTEM TERMINAL DASHBOARD
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    Route::get('/home', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        
        /**
         * TECHNICAL INVENTORY REPOSITORY
         */
        Route::get('/inventory', function () {
            $search = Request::input('search');
            $category = Request::input('category');
            $sort = Request::input('sort', 'latest');

            $query = Product::with(['variants', 'images']);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            }

            if ($category) {
                $query->where('category', $category);
            }

            switch ($sort) {
                case 'price_asc':
                    $query->addSelect(['min_price' => ProductVariant::select('price')
                        ->whereColumn('product_id', 'products.id')
                        ->orderBy('price', 'asc')->limit(1)
                    ])->orderBy('min_price', 'asc');
                    break;

                case 'price_desc':
                    $query->addSelect(['max_price' => ProductVariant::select('price')
                        ->whereColumn('product_id', 'products.id')
                        ->orderBy('price', 'desc')->limit(1)
                    ])->orderBy('max_price', 'desc');
                    break;

                case 'stock_asc':
                    $query->withSum('variants as total_stock', 'stock')
                          ->orderBy('total_stock', 'asc');
                    break;

                case 'stock_desc':
                    $query->withSum('variants as total_stock', 'stock')
                          ->orderBy('total_stock', 'desc');
                    break;

                case 'latest':
                default:
                    $query->latest();
                    break;
            }

            return Inertia::render('Admin/Inventory', [
                'products' => $query->get(),
                'filters' => [
                    'search' => $search, 
                    'category' => $category, 
                    'sort' => $sort
                ]
            ]);
        })->name('inventory');

        // PRODUCT CRUD PROTOCOLS
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::post('/products/{product}/update', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

        // PRODUCT MODIFICATION PROTOCOLS
        Route::patch('/products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('products.toggle_featured');
        Route::patch('/products/{product}/update-stock', [AdminProductController::class, 'updateStock'])->name('products.update_stock');
        
        // ACQUISITION MANIFEST (ORDERS)
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{id}', [AdminOrderController::class, 'updateStatus'])->name('orders.update');

        // PROMOTIONAL LOGIC: VOUCHER CONTROL
        Route::get('/vouchers', [AdminVoucherController::class, 'index'])->name('vouchers.index');
        Route::post('/vouchers', [AdminVoucherController::class, 'store'])->name('vouchers.store');
        Route::put('/vouchers/{voucher}', [AdminVoucherController::class, 'update'])->name('vouchers.update');
        Route::patch('/vouchers/{voucher}/toggle', [AdminVoucherController::class, 'toggle'])->name('vouchers.toggle');
        Route::delete('/vouchers/{voucher}', [AdminVoucherController::class, 'destroy'])->name('vouchers.destroy');
    });
});

require __DIR__.'/auth.php';