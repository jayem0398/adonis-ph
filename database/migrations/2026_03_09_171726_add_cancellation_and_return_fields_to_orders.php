<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cancel_reason')->nullable();
            $table->text('cancel_description')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('return_reason')->nullable();
            $table->text('return_description')->nullable();
            $table->string('return_proof_path')->nullable();
            $table->timestamp('return_requested_at')->nullable(); // Siguraduhin nandoon ito
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Safe Drop: I-check muna kung nandoon bago i-drop
            $columns = [
                'cancel_reason', 'cancel_description', 'cancelled_at',
                'return_reason', 'return_description', 'return_proof_path', 'return_requested_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};