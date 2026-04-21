<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    /**
     * MASS ASSIGNMENT PROTECTION:
     * Kung wala dito ang 'product_id', iba-block ni Laravel ang save operation.
     */
    protected $fillable = [
        'product_id',
        'image_path',
        'position'
    ];

    /**
     * RELATIONSHIP:
     * Ang bawat image ay pag-aari ng isang specific product unit.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}