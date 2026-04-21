<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VoucherController extends Controller
{
    /**
     * DISPLAY REPOSITORY
     */
    public function index()
    {
        return Inertia::render('Admin/Vouchers', [
            'vouchers' => Voucher::latest()->get()
        ]);
    }

    /**
     * INITIALIZE CODE
     */
    public function store(Request $request)
    {
        // Force uppercase before validation
        if ($request->has('code')) {
            $request->merge(['code' => strtoupper($request->code)]);
        }

        $validated = $request->validate([
            'code'        => 'required|unique:vouchers,code|max:50',
            'type'        => 'required|in:fixed,percentage',
            'value'       => 'required|numeric|min:0',
            'usage_limit' => 'required|integer|min:1',
            'expires_at'  => 'nullable|date',
        ]);

        Voucher::create($validated);

        return back()->with('success', 'Voucher manifest initialized.');
    }

    /**
     * TOGGLE STATUS PROTOCOL
     */
    public function toggle(Voucher $voucher)
    {
        $voucher->update([
            'is_active' => !$voucher->is_active
        ]);

        return back()->with('success', 'Voucher status updated.');
    }

    /**
     * PURGE PROTOCOL
     */
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return back()->with('success', 'Voucher purged from repository.');
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => 'required|unique:vouchers,code,' . $voucher->id,
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric',
            'usage_limit' => 'required|integer',
            'expires_at' => 'nullable|date',
        ]);

        $voucher->update($validated);

        return back()->with('success', 'Voucher updated successfully.');
    }
}