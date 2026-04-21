<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    // Explicitly pointing to the table just in case
    protected $table = 'product_variants';

    protected $fillable = [
        'product_id', 
        'size', 
        'price', 
        'discount_price', 
        'discount_percentage', 
        'stock', 
        'sku'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}