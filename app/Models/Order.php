<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        // CORE DATA
        'user_id', 
        'first_name', 
        'last_name', 
        'email', 
        'phone', 
        'address', 
        'city', 
        'payment_method', 
        'total_amount', 
        
        // SYSTEM STATUS
        'status',
        'fulfillment_status',
        'tracking_number',
        'voucher_code', 
        
        // TIMESTAMPS FOR LOGISTICS
        'shipped_at',
        'cancelled_at',
        'return_requested_at',

        // INCIDENT REPORTING (CANCEL/RETURN)
        'cancel_reason',
        'cancel_description',
        'return_reason',
        'return_description',
        'return_proof_path',
    ];

    /**
     * Relationship to the Acquisition Node (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to the Unit Manifest (OrderItems)
     * Selyado: Always eager load items in controller for manifest view
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}