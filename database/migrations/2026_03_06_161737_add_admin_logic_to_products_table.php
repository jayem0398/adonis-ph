<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Idagdag lang natin yung mga wala pa
            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false); 
            }
            
            if (!Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(1); 
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
