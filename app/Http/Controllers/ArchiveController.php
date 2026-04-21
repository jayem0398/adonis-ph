<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('variants');

        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        return Inertia::render('Archive/Index', [
            'products' => $query->latest()->get(),
            'filters' => $request->only(['category']),
        ]);
    }
}