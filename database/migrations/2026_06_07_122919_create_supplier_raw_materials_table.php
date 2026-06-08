<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pivot: harga, minimum pemesanan, dan ketersediaan
     * bahan baku per pemasok.
     */
    public function up(): void
    {
        Schema::create('supplier_raw_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('cascade');
            $table->decimal('price_per_unit', 12, 2);       // harga jual pemasok
            $table->decimal('minimum_order_qty', 10, 2);    // minimum pemesanan
            $table->decimal('available_stock', 10, 2)->default(0); // stok tersedia di pemasok
            $table->integer('lead_time_days')->default(1);  // estimasi hari pengiriman
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['supplier_id', 'raw_material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_raw_materials');
    }
};
