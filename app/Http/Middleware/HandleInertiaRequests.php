<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            /**
             * Authenticated User Data
             */
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'is_admin' => (bool) $request->user()->is_admin,
                ] : null,
            ],

            /**
             * Studio Flash Notifications
             * Selyadong manifest para sa StudioNotification.vue
             */
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'success_voucher' => fn () => $request->session()->get('success_voucher'),
            ],

            /**
             * Persistent Shopping Bag Stats (Database Driven)
             */
            'cartCount' => function () use ($request) {
                if (!$request->user()) {
                    return 0;
                }
                
                return (int) Cart::where('user_id', $request->user()->id)->sum('quantity');
            },
        ]);
    }
}