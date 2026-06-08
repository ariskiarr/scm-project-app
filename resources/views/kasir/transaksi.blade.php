@extends('layouts.app')

@section('title', 'Input Transaksi Penjualan')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8" x-data="posTerminal()">
    <div class="mb-8">
        <a href="{{ route('kasir.dashboard') }}" class="text-xs text-amber-600 font-bold hover:text-amber-700">&larr; Kembali ke Dashboard</a>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight mt-2">Kasir / POS Terminal</h1>
        <p class="text-slate-500 text-sm mt-1">Pilih menu kebab, sesuaikan kuantitas, dan proses transaksi di tempat.</p>
    </div>

    <!-- POS Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Products Grid (Left) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Menu Kebab</h3>
                    <!-- Search Input -->
                    <input type="text" x-model="searchQuery" placeholder="Cari menu..."
                           class="rounded-xl border border-slate-200 px-4 py-2 text-xs focus:border-amber-500 focus:outline-none w-48">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <template x-for="prod in filteredProducts()" :key="prod.id">
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 flex flex-col justify-between hover:border-amber-200 hover:bg-amber-50/20 transition cursor-pointer"
                             @click="addToCart(prod)">
                            <div>
                                <div class="w-full h-28 bg-slate-200 rounded-xl overflow-hidden mb-3 flex items-center justify-center text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                                </div>
                                <span class="font-bold text-sm text-slate-800 block" x-text="prod.name"></span>
                                <span class="text-3xs text-slate-400 font-mono block" x-text="`Kode: ${prod.code}`"></span>
                                <span class="text-xs text-amber-600 font-bold block mt-1" x-text="formatRupiah(prod.price)"></span>
                            </div>
                            <div class="mt-3 flex justify-between items-center">
                                <span class="text-3xs text-slate-400 font-medium" x-text="`Stok: ${parseInt(prod.stock)} pcs`"></span>
                                <span class="bg-amber-500 text-white rounded-lg p-1 text-2xs hover:bg-amber-600 transition">+ Tambah</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- POS Cart & Checkout (Right) -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-xl shadow-slate-100/50 sticky top-24">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Keranjang Belanja</h3>

                <!-- Cart Items List -->
                <div class="space-y-3 max-h-60 overflow-y-auto mb-4 pr-1">
                    <template x-for="(item, idx) in cart" :key="item.product_id">
                        <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl">
                            <div>
                                <span class="font-bold text-xs text-slate-800 block" x-text="item.name"></span>
                                <span class="text-3xs text-slate-400 font-medium" x-text="formatRupiah(item.price)"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="changeQty(item, -1)" class="w-6 h-6 rounded-md bg-slate-200 hover:bg-slate-300 text-slate-600 font-bold text-xs flex items-center justify-center">-</button>
                                <span class="text-xs font-bold text-slate-800 w-6 text-center" x-text="item.quantity"></span>
                                <button type="button" @click="changeQty(item, 1)" class="w-6 h-6 rounded-md bg-slate-200 hover:bg-slate-300 text-slate-600 font-bold text-xs flex items-center justify-center">+</button>
                                <button type="button" @click="removeFromCart(idx)" class="text-red-500 hover:text-red-700 text-2xs pl-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                    <template x-if="cart.length === 0">
                        <div class="py-12 text-center text-slate-400 text-xs">
                            Keranjang kosong. Klik produk di sebelah kiri untuk menambahkan.
                        </div>
                    </template>
                </div>

                <!-- Checkout Form -->
                <form action="{{ route('kasir.transaksi.store') }}" method="POST" @submit.prevent="submitForm($el)">
                    @csrf

                    <!-- Form Hidden Fields -->
                    <template x-for="(item, idx) in cart" :key="item.product_id">
                        <div>
                            <input type="hidden" :name="`items[${idx}][product_id]`" :value="item.product_id">
                            <input type="hidden" :name="`items[${idx}][quantity]`" :value="item.quantity">
                        </div>
                    </template>

                    <div class="space-y-4 border-t border-slate-50 pt-4">
                        <div>
                            <label class="block text-3xs font-semibold text-slate-500">Metode Pembayaran</label>
                            <select name="payment_method" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none">
                                <option value="cash">Tunai (Cash)</option>
                                <option value="qris">QRIS</option>
                                <option value="transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-3xs font-semibold text-slate-500">Diskon Tambahan (Rp)</label>
                            <input type="number" name="discount" x-model.number="discount" min="0" @input="calculateTotals()"
                                   class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none" placeholder="0">
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl space-y-1.5 text-xs">
                            <div class="flex justify-between text-slate-500">
                                <span>Subtotal</span>
                                <span class="font-semibold text-slate-800" x-text="`Rp ${formatRupiah(subtotal)}`"></span>
                            </div>
                            <div class="flex justify-between text-red-500" x-show="discount > 0">
                                <span>Diskon</span>
                                <span class="font-semibold" x-text="`-Rp ${formatRupiah(discount)}`"></span>
                            </div>
                            <div class="flex justify-between text-sm font-bold text-slate-800 border-t border-slate-100 pt-1.5">
                                <span>Total Bayar</span>
                                <span class="text-amber-600" x-text="`Rp ${formatRupiah(totalAmount)}`"></span>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" :disabled="cart.length === 0"
                                    class="w-full flex justify-center rounded-xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-500/10 hover:bg-amber-600 focus:outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                Cetak Struk & Bayar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function posTerminal() {
        return {
            products: @json($products),
            searchQuery: '',
            cart: [],
            discount: 0,
            subtotal: 0,
            totalAmount: 0,

            filteredProducts() {
                if (!this.searchQuery) return this.products;
                const query = this.searchQuery.toLowerCase();
                return this.products.filter(p => p.name.toLowerCase().includes(query) || p.code.toLowerCase().includes(query));
            },
            addToCart(product) {
                const item = this.cart.find(c => c.product_id === product.id);
                if (item) {
                    if (item.quantity >= parseFloat(product.stock)) {
                        alert(`Stok produk ${product.name} telah mencapai batas maksimum.`);
                        return;
                    }
                    item.quantity++;
                } else {
                    this.cart.push({
                        product_id: product.id,
                        name: product.name,
                        price: parseFloat(product.price),
                        quantity: 1,
                        maxStock: parseFloat(product.stock)
                    });
                }
                this.calculateTotals();
            },
            changeQty(item, val) {
                item.quantity += val;
                if (item.quantity > item.maxStock) {
                    alert(`Stok produk ${item.name} hanya tersedia ${item.maxStock} pcs.`);
                    item.quantity = item.maxStock;
                }
                if (item.quantity <= 0) {
                    const idx = this.cart.indexOf(item);
                    this.removeFromCart(idx);
                }
                this.calculateTotals();
            },
            removeFromCart(idx) {
                this.cart.splice(idx, 1);
                this.calculateTotals();
            },
            calculateTotals() {
                this.subtotal = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                this.totalAmount = Math.max(0, this.subtotal - (this.discount || 0));
            },
            formatRupiah(val) {
                return new Intl.NumberFormat('id-ID').format(val);
            },
            submitForm(form) {
                if (this.cart.length === 0) return;
                form.submit();
            }
        }
    }
</script>
@endsection
