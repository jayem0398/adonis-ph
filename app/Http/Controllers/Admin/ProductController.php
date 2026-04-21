<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * STORE: Create new unit with specific thumbnail/gallery separation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'files' => 'required|array|min:1',
            'variants' => 'required|array|min:1',
            'main_file_index' => 'required|integer',
        ]);

        return DB::transaction(function () use ($request) {
            $mainFileIndex = $request->input('main_file_index', 0);
            $uploadedPaths = [];

            foreach ($request->file('files') as $file) {
                $uploadedPaths[] = $file->store('products', 'public');
            }

            $product = Product::create([
                'name' => $request->name,
                'category' => $request->category,
                'description' => $request->description ?? 'Studio archival piece.',
                'image_path' => $uploadedPaths[$mainFileIndex], 
                'is_featured' => $request->is_featured ?? false,
            ]);

            foreach ($uploadedPaths as $index => $path) {
                if ($index == $mainFileIndex) continue; 
                $product->images()->create(['image_path' => $path, 'position' => $index]);
            }

            foreach ($request->variants as $variant) {
                $product->variants()->create([
                    'size' => $variant['size'],
                    'price' => $variant['price'],
                    'stock' => $variant['stock'],
                    'discount_percentage' => $variant['discount_percentage'] ?? 0,
                    'sku' => strtoupper(Str::slug($product->name) . '-' . $variant['size'] . '-' . Str::random(4)),
                ]);
            }

            return redirect()->back()->with('success', 'Unit_Committed_To_Archive');
        });
    }

    /**
     * UPDATE: Synchronize images and variants.
     * SELYADO FIX: Tumatanggap na ito ng spoofed PUT request via POST route.
     */
    public function update(Request $request, $id)
    {
        // 1. Hanapin ang product manually para iwas sa Model Binding issues during spoofing
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'variants' => 'required|array|min:1',
        ]);

        return DB::transaction(function () use ($request, $product) {
            // Update basic info
            $product->update([
                'name' => $request->name,
                'category' => $request->category,
                'description' => $request->description,
                'is_featured' => filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN),
            ]);

            // Image Sync: Alisin ang mga tinanggal sa UI
            $existingImages = $request->input('existing_images', []);
            $existingIds = collect($existingImages)->pluck('id')->filter()->toArray();
            
            $toDelete = $product->images()->whereNotIn('id', $existingIds)->get();
            foreach ($toDelete as $img) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }

            // Append New Files to Gallery
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }

            // Thumbnail Logic: Kung ang main thumbnail file ay pinalitan o nawala
            if (!$product->image_path || !Storage::disk('public')->exists($product->image_path)) {
                if ($first = $product->images()->first()) {
                    $product->update(['image_path' => $first->image_path]);
                }
            }

            // Variant Sync: Update existing, Create new, Delete missing
            $variantIds = [];
            foreach ($request->variants as $vData) {
                $variant = $product->variants()->updateOrCreate(
                    ['id' => $vData['id'] ?? null],
                    [
                        'size' => $vData['size'],
                        'price' => $vData['price'],
                        'stock' => $vData['stock'],
                        'discount_percentage' => $vData['discount_percentage'] ?? 0,
                        'sku' => $vData['sku'] ?? strtoupper(Str::slug($product->name) . '-' . $vData['size'] . '-' . Str::random(4)),
                    ]
                );
                $variantIds[] = $variant->id;
            }
            $product->variants()->whereNotIn('id', $variantIds)->delete();

            return redirect()->back()->with('success', 'Archive_Updated_Successfully');
        });
    }

    /**
     * UPDATE STOCK: Quick adjustment for inventory management.
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate(['variants' => 'required|array']);
        foreach ($request->variants as $vData) {
            ProductVariant::where('id', $vData['id'])->update(['stock' => $vData['stock']]);
        }
        return redirect()->back()->with('success', 'Inventory_Levels_Synced');
    }

    /**
     * TOGGLE FEATURED: Highlight or un-highlight unit.
     */
    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);
        return redirect()->back()->with('success', 'Featured_Status_Toggled');
    }

    /**
     * DESTROY: Purge unit and all physical files.
     */
    public function destroy(Product $product)
    {
        return DB::transaction(function () use ($product) {
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->image_path);
            }
            Storage::disk('public')->delete($product->image_path);
            
            $product->delete();
            return redirect()->back()->with('success', 'Unit_Purged_From_System');
        });
    }
}