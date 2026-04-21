<?php

namespace App\Http\Controllers;

use App\Models\{Product, ProductVariant, ProductImage, Cart};
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\{DB, Str, Storage, Auth};

class ProductController extends Controller
{
    public function index(Request $request) {
        $featuredProducts = Product::with(['variants', 'images'])->where('is_featured', true)->latest()->take(8)->get();
        $allProducts = Product::with(['variants', 'images'])
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->latest()->get();

        return Inertia::render('Welcome', [
            'products' => $featuredProducts,
            'allProducts' => $allProducts,
            'cartCount' => Auth::check() ? (int) Cart::where('user_id', Auth::id())->sum('quantity') : 0,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    public function show($id) {
        return Inertia::render('Archive/Show', [
            'product' => Product::with(['variants', 'images'])->findOrFail($id),
            'cartCount' => Auth::check() ? (int) Cart::where('user_id', Auth::id())->sum('quantity') : 0,
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'files' => 'required|array|min:1',
            'files.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'variants' => 'required|array|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $mainImagePath = $request->file('files')[0]->store('products', 'public');

            $product = Product::create([
                'name' => $request->name,
                'category' => $request->category,
                'description' => $request->description ?? 'Studio archival piece.',
                'image_path' => $mainImagePath,
                'is_featured' => $request->is_featured ?? false,
            ]);

            foreach ($request->file('files') as $index => $file) {
                $product->images()->create([
                    'image_path' => $file->store('products', 'public'),
                    'position' => $index
                ]);
            }

            foreach ($request->variants as $v) {
                $product->variants()->create([
                    'size' => $v['size'],
                    'price' => $v['price'],
                    'stock' => $v['stock'],
                    'discount_percentage' => $v['discount_percentage'] ?? 0,
                    'sku' => strtoupper(Str::slug($product->name).'-'.$v['size'].'-'.Str::random(4)),
                ]);
            }
            return redirect()->back()->with('success', 'Unit archived.');
        });
    }

    public function update(Request $request, Product $product) {
        $request->validate(['name' => 'required', 'category' => 'required', 'variants' => 'required|array']);

        return DB::transaction(function () use ($request, $product) {
            $product->update($request->only(['name', 'category', 'description', 'is_featured']));

            $existingIds = collect($request->existing_images)->pluck('id')->toArray();
            foreach ($product->images()->whereNotIn('id', $existingIds)->get() as $img) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $product->images()->create(['image_path' => $file->store('products', 'public')]);
                }
            }

            if ($first = $product->images()->first()) $product->update(['image_path' => $first->image_path]);

            $variantIds = [];
            foreach ($request->variants as $v) {
                $variant = $product->variants()->updateOrCreate(['id' => $v['id'] ?? null], [
                    'size' => $v['size'], 
                    'price' => $v['price'], 
                    'stock' => $v['stock'],
                    'discount_percentage' => $v['discount_percentage'] ?? 0,
                    'sku' => $v['sku'] ?? strtoupper(Str::slug($product->name).'-'.$v['size'].'-'.Str::random(4)),
                ]);
                $variantIds[] = $variant->id;
            }
            $product->variants()->whereNotIn('id', $variantIds)->delete();

            return redirect()->back()->with('success', 'Archive updated.');
        });
    }

    public function destroy(Product $product) {
        return DB::transaction(function () use ($product) {
            foreach ($product->images as $img) Storage::disk('public')->delete($img->image_path);
            $product->delete();
            return redirect()->back()->with('success', 'Purged.');
        });
    }

    public function toggleFeatured(Product $product) {
        $product->update(['is_featured' => !$product->is_featured]);
        return redirect()->back()->with('success', 'Status toggled.');
    }
}