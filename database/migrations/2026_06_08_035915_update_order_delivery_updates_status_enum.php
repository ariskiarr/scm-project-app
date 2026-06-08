<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Memperluas ENUM status pada order_delivery_updates.
     * Menambahkan 'pending', 'confirmed', 'processing' yang digunakan
     * saat pembuatan pesanan dan update status oleh kasir.
     */
    public function up(): void
    {
        Schema::table('order_delivery_updates', function (Blueprint $table) {
            // MySQL doesn't allow adding values to ENUM directly.
            // We need to alter the column with the full new ENUM definition.
            $table->string('status', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_delivery_updates', function (Blueprint $table) {
            $table->enum('status', [
                'ready_to_ship',
                'picked_up',
                'in_transit',
                'delivered',
                'failed_delivery',
            ])->change();
        });
    }
};
