<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'slug',
        'image_path', 
        'category', 
        'is_featured', 
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (!$product->slug) {
                $product->slug = Str::slug($product->name) . '-' . uniqid();
            }
        });
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}