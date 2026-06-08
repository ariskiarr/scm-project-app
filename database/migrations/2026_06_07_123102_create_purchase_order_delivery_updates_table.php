<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat pembaruan status pengiriman bahan baku dari pemasok.
     */
    public function up(): void
    {
        Schema::create('purchase_order_delivery_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onDelete('restrict');
            $table->enum('status', [
                'confirmed',
                'preparing',
                'shipped',
                'in_transit',
                'received',
                'cancelled',
            ]);
            $table->text('description')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_delivery_updates');
    }
};
