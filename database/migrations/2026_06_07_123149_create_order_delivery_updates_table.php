<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat update status pengiriman pesanan pelanggan oleh kurir.
     */
    public function up(): void
    {
        Schema::create('order_delivery_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_order_id')->constrained('customer_orders')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onDelete('restrict');
            $table->enum('status', [
                'ready_to_ship',
                'picked_up',
                'in_transit',
                'delivered',
                'failed_delivery',
            ]);
            $table->string('location')->nullable();     // lokasi saat update
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_delivery_updates');
    }
};
