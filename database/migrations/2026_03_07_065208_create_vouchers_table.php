<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Ang promo code (e.g., ADONIS50)
            $table->enum('type', ['fixed', 'percentage']); // PHP bawas o % bawas
            $table->decimal('value', 10, 2); // Magkano ang discount
            
            // LIMITED USAGE PROTOCOLS
            $table->integer('usage_limit')->default(1); // Ilang users lang pwedeng gumamit
            $table->integer('used_count')->default(0);  // Counter kung ilan na ang nakagamit
            
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable(); // Optional expiration date
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};