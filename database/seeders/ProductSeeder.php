<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'ARCHIVE COMPRESSION TANK V1',
                'category' => 'Tank Binder',
                'description' => 'Precision-engineered silhouette control.',
                'image_path' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?q=80&w=1000',
                'price' => 1450
            ],
            [
                'name' => 'STUDIO HALF-LENGTH BINDER',
                'category' => 'Half Binder',
                'description' => 'Mid-section archival piece. Engineered for movement.',
                'image_path' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?q=80&w=1000',
                'price' => 1200
            ],
            [
                'name' => 'INCLUSIVE ESSENTIAL PIECE',
                'category' => 'Tank Binder',
                'description' => 'Core repository unit. Minimalist construction.',
                'image_path' => 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?q=80&w=1000',
                'price' => 1650
            ],
            [
                'name' => 'LINEAR ARCHIVE TOP',
                'category' => 'Essentials',
                'description' => 'Documented silhouette. Industrial grade fabric.',
                'image_path' => 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?q=80&w=1000',
                'price' => 1800
            ],
            [
                'name' => 'KINETIC HALF-LENGTH UNIT',
                'category' => 'Half Binder',
                'description' => 'Studio series 02. Reinforced stitching.',
                'image_path' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=1000',
                'price' => 1350
            ],
            [
                'name' => 'MONOLITH TANK V2',
                'category' => 'Limited Edition',
                'description' => 'Limited repository access. Stark white archive.',
                'image_path' => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?q=80&w=1000',
                'price' => 2100
            ],
            [
                'name' => 'MODULAR BINDER SYSTEM',
                'category' => 'Essentials',
                'description' => 'Versatile archival construction. Adjustable compression.',
                'image_path' => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?q=80&w=1000',
                'price' => 1950
            ],
            [
                'name' => 'GENESIS CORE BINDER',
                'category' => 'Limited Edition',
                'description' => 'The original documentation. Heritage archive.',
                'image_path' => 'https://images.unsplash.com/photo-1552374196-1ab2a1c593e8?q=80&w=1000',
                'price' => 2500
            ],
        ];

        foreach ($products as $p) {
            $product = Product::create([
                'name' => $p['name'],
                'category' => $p['category'],
                'description' => $p['description'],
                'image_path' => $p['image_path'],
            ]);

            // Create standard variants for each
            $product->variants()->createMany([
                ['size' => 'S', 'price' => $p['price'], 'stock' => 10],
                ['size' => 'M', 'price' => $p['price'], 'stock' => 15],
                ['size' => 'L', 'price' => $p['price'], 'stock' => 5],
            ]);
        }
    }
}