<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir | TOKOHVERA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <div x-data="posTerminal(@js($products))" x-init="initialize()" class="mx-auto min-h-screen max-w-[1440px] px-3 py-3 sm:px-5">
        <header class="mb-3 flex items-center justify-between gap-4 border-b border-slate-200 px-1 pb-3">
            <div>
                <p class="text-base font-black leading-tight tracking-tight text-slate-900 sm:text-lg">TOKOHVERA</p>
                <p class="mt-1 text-[11px] text-slate-500">Kasir: {{ auth()->user()->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-slate-500">No. Transaksi</p>
                <p class="font-mono text-xs font-bold text-slate-800" x-text="transactionNumber"></p>
                <p class="text-[10px] text-slate-500">{{ now()->format('d/m/Y, H.i') }}</p>
            </div>
        </header>

        <main class="grid items-start gap-3 lg:grid-cols-[minmax(0,1fr)_272px]">
            <section class="min-w-0 space-y-3">
                <div class="overflow-visible rounded-md border border-slate-200 bg-white">
                    <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
                        <h1 class="text-xs font-bold text-slate-800">Tambah Barang</h1>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="openInventoryModal()" class="inline-flex h-7 items-center gap-1 rounded border border-slate-200 bg-white px-2 text-[10px] font-semibold text-slate-700 hover:bg-slate-100">Kelola</button>
                            <button type="button" @click="openProductModal()" class="inline-flex h-7 items-center gap-1 rounded border border-slate-200 bg-white px-2 text-[10px] font-semibold text-blue-700 hover:bg-blue-50">
                                <span aria-hidden="true">+</span>
                                Barang Baru
                            </button>
                        </div>
                    </div>
                    <div class="space-y-2.5 p-3">
                        <div class="relative flex gap-1.5">
                            <input x-model="query" @focus="showResults = true" @keydown.enter.prevent="addFirstResult()" type="search" autocomplete="off" placeholder="Cari nama barang / kode / barcode..." class="h-9 min-w-0 flex-1 rounded border-slate-300 px-2.5 text-xs shadow-none placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <button type="button" @click="addFirstResult()" class="h-9 rounded bg-blue-600 px-3 text-xs font-semibold text-white hover:bg-blue-700">Cari</button>
                            <div x-cloak x-show="showResults && query.trim()" @click.outside="showResults = false" class="absolute left-0 right-12 top-10 z-20 max-h-64 overflow-y-auto rounded border border-slate-200 bg-white shadow-lg">
                                <template x-for="product in filteredProducts" :key="product.id">
                                    <button type="button" @click="addProduct(product)" class="flex w-full items-center justify-between gap-3 border-b border-slate-100 px-3 py-2 text-left last:border-0 hover:bg-blue-50">
                                        <span class="min-w-0">
                                            <span class="block truncate text-xs font-semibold text-slate-800" x-text="product.name"></span>
                                            <span class="text-[10px] text-slate-500" x-text="product.category || 'Tanpa kategori'"></span>
                                        </span>
                                        <span class="shrink-0 text-right">
                                            <span class="block text-xs font-bold text-slate-800" x-text="formatCurrency(product.price)"></span>
                                            <span class="text-[10px] text-slate-500" x-text="`Stok ${product.stock}`"></span>
                                        </span>
                                    </button>
                                </template>
                                <div x-show="filteredProducts.length === 0" class="px-3 py-3 text-center">
                                    <p class="text-xs text-slate-500">Barang tidak ditemukan.</p>
                                    <button type="button" @click="openProductModal()" class="mt-1 inline-block text-xs font-semibold text-blue-700 hover:underline">+ Tambah barang baru</button>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <label class="block text-[10px] font-medium text-slate-600">
                                Pelanggan
                                <select x-model="customer" class="mt-1 h-8 w-full rounded border-slate-300 py-1 text-xs focus:border-blue-500 focus:ring-blue-500">
                                    <option value="Umum">Umum</option>
                                    <option value="Member">Member</option>
                                </select>
                            </label>
                            <label class="block text-[10px] font-medium text-slate-600">
                                No. Member / HP
                                <input x-model="memberNumber" type="text" inputmode="tel" placeholder="Opsional" class="mt-1 h-8 w-full rounded border-slate-300 px-2 text-xs focus:border-blue-500 focus:ring-blue-500">
                            </label>
                        </div>
                        <p x-cloak x-show="notice" x-text="notice" class="text-xs font-medium text-emerald-700" role="status"></p>
                        <p x-cloak x-show="errorMessage" x-text="errorMessage" class="text-xs font-medium text-red-600" role="alert"></p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-md border border-slate-200 bg-white">
                    <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2">
                        <h2 class="text-xs font-bold text-slate-800">Keranjang Belanja</h2>
                        <span class="text-xs font-bold text-slate-700" x-text="`${totalItems} Item`"></span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[580px] text-left text-[10px]">
                            <thead class="border-b border-slate-200 bg-slate-50/70 text-slate-600">
                                <tr>
                                    <th class="w-9 px-3 py-2 font-semibold">No</th>
                                    <th class="px-2 py-2 font-semibold">Barang</th>
                                    <th class="w-28 px-2 py-2 text-right font-semibold">Harga</th>
                                    <th class="w-24 px-2 py-2 text-center font-semibold">Qty</th>
                                    <th class="w-28 px-2 py-2 text-right font-semibold">Subtotal</th>
                                    <th class="w-9 px-2 py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="item.id">
                                    <tr class="border-b border-slate-100 last:border-0">
                                        <td class="px-3 py-2.5 text-slate-500" x-text="index + 1"></td>
                                        <td class="px-2 py-2.5">
                                            <p class="font-semibold text-slate-800" x-text="item.name"></p>
                                            <p class="text-[9px] text-slate-500" x-text="item.category || 'Umum'"></p>
                                        </td>
                                        <td class="px-2 py-2.5 text-right tabular-nums" x-text="formatCurrency(item.price)"></td>
                                        <td class="px-2 py-2.5">
                                            <div class="mx-auto flex w-fit items-center gap-1">
                                                <button type="button" @click="decreaseQuantity(item)" :aria-label="`Kurangi ${item.name}`" class="h-6 w-6 rounded border border-slate-200 text-sm leading-none hover:bg-slate-100">-</button>
                                                <span class="w-5 text-center font-semibold tabular-nums" x-text="item.quantity"></span>
                                                <button type="button" @click="increaseQuantity(item)" :aria-label="`Tambah ${item.name}`" class="h-6 w-6 rounded border border-slate-200 text-sm leading-none hover:bg-slate-100">+</button>
                                            </div>
                                        </td>
                                        <td class="px-2 py-2.5 text-right font-semibold tabular-nums" x-text="formatCurrency(item.price * item.quantity)"></td>
                                        <td class="px-2 py-2.5 text-center">
                                            <button type="button" @click="removeProduct(item)" :aria-label="`Hapus ${item.name}`" class="rounded px-1.5 py-1 text-xs text-red-600 hover:bg-red-50">×</button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="items.length === 0">
                                    <td colspan="6" class="h-24 px-3 text-center text-[10px] text-slate-500">Keranjang masih kosong.<br>Silakan cari atau scan barang.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <aside class="rounded-md border border-slate-200 bg-white">
                <div class="border-b border-slate-200 bg-slate-50 px-3 py-2">
                    <h2 class="text-xs font-bold text-slate-800">Ringkasan Pembayaran</h2>
                </div>
                <div class="space-y-2 p-3">
                    <div class="flex items-center justify-between text-[10px]">
                        <span>Total Item</span>
                        <span class="font-semibold tabular-nums" x-text="totalItems"></span>
                    </div>
                    <div class="flex items-center justify-between text-[10px]">
                        <span>Subtotal</span>
                        <span class="font-semibold tabular-nums" x-text="formatCurrency(subtotal)"></span>
                    </div>

                    <label class="flex items-center justify-between gap-2 text-[10px]">
                        <span>Diskon (%)</span>
                        <input x-model.number="discountPercent" type="number" min="0" max="100" step="0.1" class="h-7 w-24 rounded border-slate-300 px-2 text-right text-xs tabular-nums focus:border-blue-500 focus:ring-blue-500">
                    </label>
                    <label class="flex items-center justify-between gap-2 text-[10px]">
                        <span>Diskon (Rp)</span>
                        <input x-model.number="discountAmount" type="number" min="0" step="500" class="h-7 w-24 rounded border-slate-300 px-2 text-right text-xs tabular-nums focus:border-blue-500 focus:ring-blue-500">
                    </label>
                    <label class="flex items-center justify-between gap-2 text-[10px]">
                        <span>Pajak / PPN (%)</span>
                        <input x-model.number="taxPercent" type="number" min="0" max="100" step="0.1" class="h-7 w-24 rounded border-slate-300 px-2 text-right text-xs tabular-nums focus:border-blue-500 focus:ring-blue-500">
                    </label>
                    <label class="flex items-center justify-between gap-2 text-[10px]">
                        <span>Biaya Lain</span>
                        <input x-model.number="otherFees" type="number" min="0" step="500" class="h-7 w-24 rounded border-slate-300 px-2 text-right text-xs tabular-nums focus:border-blue-500 focus:ring-blue-500">
                    </label>

                    <div class="rounded bg-slate-50 px-2.5 py-2.5">
                        <p class="text-[9px] uppercase text-slate-500">Total Akhir</p>
                        <p class="text-xl font-black leading-tight tabular-nums text-slate-900" x-text="formatCurrency(total)"></p>
                    </div>

                    <div class="space-y-2 rounded border border-blue-200 bg-blue-50/70 p-2.5">
                        <label class="block text-[10px] font-semibold text-slate-700">
                            Uang Dibayar
                            <input x-model.number="paidAmount" type="number" min="0" step="1000" class="mt-1 h-9 w-full rounded border-blue-300 bg-white px-2 text-right text-base font-bold tabular-nums text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </label>
                        <div>
                            <p class="mb-1.5 text-[10px] font-semibold text-slate-700">Metode Pembayaran</p>
                            <div class="grid grid-cols-3 gap-1">
                                <template x-for="method in paymentMethods" :key="method">
                                    <button type="button" @click="paymentMethod = method" :class="paymentMethod === method ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'" class="min-h-6 rounded border px-1 text-[9px] font-medium" x-text="method"></button>
                                </template>
                            </div>
                        </div>
                        <div class="rounded bg-emerald-100 px-2.5 py-2">
                            <p class="text-[9px] uppercase text-emerald-800">Kembalian</p>
                            <p class="text-base font-black leading-tight tabular-nums text-emerald-900" x-text="formatCurrency(change)"></p>
                        </div>
                    </div>

                    <div x-cloak x-show="heldSale" class="flex items-center justify-between rounded bg-amber-50 px-2 py-1.5 text-[10px] text-amber-900">
                        <span>Ada transaksi yang ditahan</span>
                        <button type="button" @click="resumeSale()" class="font-bold underline">Lanjutkan</button>
                    </div>
                    <p x-cloak x-show="errorMessage" x-text="errorMessage" class="text-[10px] font-medium text-red-600" role="alert"></p>

                    <div class="grid grid-cols-2 gap-1.5 pt-1">
                        <button type="button" @click="holdSale()" class="h-8 rounded bg-amber-400 text-[10px] font-bold text-slate-900 hover:bg-amber-500">Tahan</button>
                        <button type="button" @click="cancelSale()" class="h-8 rounded bg-rose-600 text-[10px] font-bold text-white hover:bg-rose-700">Batal</button>
                    </div>
                    <button type="button" @click="pay()" :disabled="items.length === 0 || receipt" class="h-9 w-full rounded bg-emerald-700 text-[11px] font-bold text-white hover:bg-emerald-800 disabled:cursor-not-allowed disabled:bg-slate-300">Bayar &amp; Cetak</button>
                    <button x-cloak x-show="receipt" type="button" @click="startNewSale()" class="w-full text-center text-[10px] font-semibold text-blue-700 hover:underline">Transaksi Baru</button>
                </div>
            </aside>
        </main>

    <div x-cloak x-show="showProductModal" @keydown.escape.window="closeProductModal()" class="fixed inset-0 z-40 flex items-center justify-center bg-slate-950/40 p-4" @click.self="closeProductModal()">
        <section role="dialog" aria-modal="true" aria-labelledby="new-product-title" class="w-full max-w-md rounded-md border border-slate-200 bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <div>
                    <h2 id="new-product-title" class="text-sm font-bold text-slate-900" x-text="editingProductId ? 'Edit Barang' : 'Tambah Barang Baru'"></h2>
                    <p class="mt-0.5 text-[10px] text-slate-500">Perubahan langsung tersimpan ke inventaris.</p>
                </div>
                <button type="button" @click="closeProductModal()" aria-label="Tutup form" class="h-7 w-7 rounded text-lg leading-none text-slate-500 hover:bg-slate-100">×</button>
            </div>
            <form @submit.prevent="saveProduct()" class="space-y-3 p-4">
                <label class="block text-xs font-medium text-slate-700">
                    Nama Barang
                    <input x-model="productForm.name" type="text" maxlength="255" required class="mt-1 h-9 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </label>
                <label class="block text-xs font-medium text-slate-700">
                    Kategori
                    <input x-model="productForm.category" type="text" maxlength="100" class="mt-1 h-9 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block text-xs font-medium text-slate-700">
                        Harga (Rp)
                        <input x-model.number="productForm.price" type="number" min="0" step="1" required class="mt-1 h-9 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </label>
                    <label class="block text-xs font-medium text-slate-700">
                        Stok Awal
                        <input x-model.number="productForm.stock" type="number" min="0" step="1" required class="mt-1 h-9 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </label>
                </div>
                <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-700">
                    <input x-model="productForm.is_active" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    Barang aktif dan bisa dijual
                </label>
                <p x-cloak x-show="productError" x-text="productError" class="text-xs font-medium text-red-600" role="alert"></p>
                <div class="flex justify-end gap-2 border-t border-slate-100 pt-3">
                    <button type="button" @click="closeProductModal()" class="h-9 rounded border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" :disabled="savingProduct" class="h-9 rounded bg-blue-600 px-4 text-xs font-semibold text-white hover:bg-blue-700 disabled:cursor-wait disabled:opacity-60" x-text="savingProduct ? 'Menyimpan...' : (editingProductId ? 'Simpan Perubahan' : 'Simpan & Tambah')"></button>
                </div>
            </form>
        </section>
    </div>

    <div x-cloak x-show="showInventoryModal" @keydown.escape.window="showInventoryModal = false" class="fixed inset-0 z-40 flex items-center justify-center bg-slate-950/40 p-4" @click.self="showInventoryModal = false">
        <section role="dialog" aria-modal="true" aria-labelledby="inventory-title" class="w-full max-w-xl rounded-md border border-slate-200 bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <div>
                    <h2 id="inventory-title" class="text-sm font-bold text-slate-900">Kelola Barang</h2>
                    <p class="mt-0.5 text-[10px] text-slate-500" x-text="`${products.length} barang di inventaris`"></p>
                </div>
                <button type="button" @click="showInventoryModal = false" aria-label="Tutup inventaris" class="h-7 w-7 rounded text-lg leading-none text-slate-500 hover:bg-slate-100">×</button>
            </div>
            <div class="space-y-2 p-3">
                <input x-model="inventoryQuery" type="search" placeholder="Cari barang di inventaris..." class="h-9 w-full rounded border-slate-300 px-2.5 text-xs focus:border-blue-500 focus:ring-blue-500">
                <div class="max-h-[55vh] divide-y divide-slate-100 overflow-y-auto rounded border border-slate-200">
                    <template x-for="product in managedProducts" :key="product.id">
                        <div class="flex items-center justify-between gap-3 px-3 py-2.5">
                            <div class="min-w-0">
                                <p class="truncate text-xs font-semibold text-slate-800" x-text="product.name"></p>
                                <p class="mt-0.5 text-[10px] text-slate-500" x-text="`${product.category || 'Umum'} · ${formatCurrency(product.price)} · Stok ${product.stock}`"></p>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <span class="rounded px-1.5 py-1 text-[9px] font-semibold" :class="product.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'" x-text="product.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                <button type="button" @click="editProduct(product)" class="h-7 rounded border border-slate-200 px-2 text-[10px] font-semibold text-blue-700 hover:bg-blue-50">Edit</button>
                                <button type="button" @click="deleteProduct(product)" :disabled="savingProduct" class="h-7 rounded border border-rose-200 px-2 text-[10px] font-semibold text-rose-700 hover:bg-rose-50 disabled:opacity-50">Hapus</button>
                            </div>
                        </div>
                    </template>
                    <p x-show="managedProducts.length === 0" class="px-3 py-8 text-center text-xs text-slate-500">Tidak ada barang yang cocok.</p>
                </div>
                <p x-cloak x-show="productError" x-text="productError" class="text-xs font-medium text-red-600" role="alert"></p>
            </div>
            <div class="flex justify-end border-t border-slate-100 px-3 py-2">
                <button type="button" @click="showInventoryModal = false" class="h-8 rounded border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Tutup</button>
            </div>
        </section>
    </div>

    <section id="receipt" x-show="receipt" class="fixed left-[-10000px] top-0 w-[72mm] bg-white p-3 font-mono text-[11px] text-black">
        <div class="text-center">
            <p class="text-sm font-bold">TOKOHVERA</p>
            <p class="mt-1">Struk Pembelian</p>
            <p class="my-2 border-b border-dashed border-black"></p>
        </div>
        <div class="space-y-1">
            <p>No: <span x-text="receipt?.transactionNumber"></span></p>
            <p>Tanggal: <span x-text="receipt?.date"></span></p>
            <p>Pelanggan: <span x-text="receipt?.customer"></span></p>
        </div>
        <p class="my-2 border-b border-dashed border-black"></p>
        <template x-for="item in (receipt?.items || [])" :key="item.id">
            <div class="mb-1">
                <p x-text="item.name"></p>
                <p class="flex justify-between"><span x-text="`${item.quantity} x ${formatCurrency(item.price)}`"></span><span x-text="formatCurrency(item.quantity * item.price)"></span></p>
            </div>
        </template>
        <p class="my-2 border-b border-dashed border-black"></p>
        <div class="space-y-1">
            <p class="flex justify-between"><span>Subtotal</span><span x-text="formatCurrency(receipt?.subtotal || 0)"></span></p>
            <p class="flex justify-between"><span>Diskon</span><span x-text="formatCurrency(receipt?.discount || 0)"></span></p>
            <p class="flex justify-between"><span>Pajak</span><span x-text="formatCurrency(receipt?.tax || 0)"></span></p>
            <p class="flex justify-between"><span>Biaya lain</span><span x-text="formatCurrency(receipt?.otherFees || 0)"></span></p>
            <p class="flex justify-between font-bold"><span>Total</span><span x-text="formatCurrency(receipt?.total || 0)"></span></p>
            <p class="flex justify-between"><span x-text="receipt?.paymentMethod"></span><span x-text="formatCurrency(receipt?.paidAmount || 0)"></span></p>
            <p class="flex justify-between"><span>Kembalian</span><span x-text="formatCurrency(receipt?.change || 0)"></span></p>
        </div>
        <p class="my-2 border-b border-dashed border-black"></p>
        <p class="text-center">Terima kasih telah berbelanja</p>
    </section>

    <script>
        window.posTerminal = (products) => ({
            products,
            query: '',
            showResults: false,
            customer: 'Umum',
            memberNumber: '',
            items: [],
            discountPercent: 0,
            discountAmount: 0,
            taxPercent: 0,
            otherFees: 0,
            paidAmount: 0,
            paymentMethod: 'Tunai',
            paymentMethods: ['Tunai', 'QRIS', 'Debit', 'Kredit', 'E-Wallet', 'Transfer'],
            errorMessage: '',
            notice: '',
            receipt: null,
            showProductModal: false,
            savingProduct: false,
            productError: '',
            showInventoryModal: false,
            inventoryQuery: '',
            editingProductId: null,
            productForm: { name: '', category: '', price: '', stock: 1, is_active: true },
            transactionNumber: `TRX-${new Date().toISOString().slice(2, 10).replaceAll('-', '')}-${Math.floor(1000 + Math.random() * 9000)}`,
            get filteredProducts() {
                const search = this.query.trim().toLocaleLowerCase('id-ID');

                return this.products
                    .filter((product) => product.is_active && Number(product.stock) > 0)
                    .filter((product) => !search || `${product.name} ${product.category || ''} ${product.id}`.toLocaleLowerCase('id-ID').includes(search))
                    .slice(0, 8);
            },
            get managedProducts() {
                const search = this.inventoryQuery.trim().toLocaleLowerCase('id-ID');

                return this.products.filter((product) => !search || `${product.name} ${product.category || ''}`.toLocaleLowerCase('id-ID').includes(search));
            },
            get totalItems() {
                return this.items.reduce((total, item) => total + item.quantity, 0);
            },
            get subtotal() {
                return this.items.reduce((total, item) => total + Number(item.price) * item.quantity, 0);
            },
            get discount() {
                const percentDiscount = this.subtotal * Math.min(100, Math.max(0, Number(this.discountPercent) || 0)) / 100;

                return Math.min(this.subtotal, percentDiscount + Math.max(0, Number(this.discountAmount) || 0));
            },
            get tax() {
                return (this.subtotal - this.discount) * Math.min(100, Math.max(0, Number(this.taxPercent) || 0)) / 100;
            },
            get total() {
                return Math.max(0, this.subtotal - this.discount + this.tax + Math.max(0, Number(this.otherFees) || 0));
            },
            get change() {
                return Math.max(0, (Number(this.paidAmount) || 0) - this.total);
            },
            get heldSale() {
                return Boolean(localStorage.getItem('minimarket-held-sale'));
            },
            initialize() {
                this.$watch('items', () => {
                    this.paidAmount = this.items.length === 0 ? 0 : Math.max(this.paidAmount, this.total);
                });
            },
            formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0,
                }).format(Number(amount) || 0);
            },
            openProductModal() {
                this.productError = '';
                this.editingProductId = null;
                this.productForm = { name: this.query.trim(), category: '', price: '', stock: 1, is_active: true };
                this.showProductModal = true;
            },
            closeProductModal() {
                this.showProductModal = false;
                this.editingProductId = null;
            },
            openInventoryModal() {
                this.productError = '';
                this.inventoryQuery = '';
                this.showInventoryModal = true;
            },
            editProduct(product) {
                this.editingProductId = product.id;
                this.productForm = {
                    name: product.name,
                    category: product.category || '',
                    price: Number(product.price),
                    stock: Number(product.stock),
                    is_active: Boolean(product.is_active),
                };
                this.productError = '';
                this.showInventoryModal = false;
                this.showProductModal = true;
            },
            async saveProduct() {
                if (this.savingProduct) return;

                this.savingProduct = true;
                this.productError = '';
                const editingId = this.editingProductId;
                const endpoint = editingId ? @json(url('/products')) + '/' + editingId : @json(route('products.store'));

                try {
                    const response = await fetch(endpoint, {
                        method: editingId ? 'PUT' : 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(this.productForm),
                    });
                    const result = await response.json();

                    if (!response.ok) {
                        this.productError = Object.values(result.errors || {}).flat().join(' ') || result.message || 'Barang gagal disimpan.';
                        return;
                    }

                    const product = {
                        ...result.product,
                        price: Number(result.product.price),
                        stock: Number(result.product.stock),
                        is_active: Boolean(result.product.is_active),
                    };

                    if (editingId) {
                        const productIndex = this.products.findIndex((item) => item.id === editingId);
                        if (productIndex >= 0) this.products.splice(productIndex, 1, product);

                        const cartItem = this.items.find((item) => item.id === editingId);
                        if (cartItem && (!product.is_active || product.stock === 0)) {
                            this.removeProduct(cartItem);
                        } else if (cartItem) {
                            Object.assign(cartItem, product);
                            cartItem.quantity = Math.min(cartItem.quantity, product.stock);
                        }
                    } else {
                        this.products.unshift(product);
                    }

                    this.showProductModal = false;
                    this.editingProductId = null;

                    if (editingId) {
                        this.showInventoryModal = true;
                        this.notice = `${product.name} berhasil diperbarui.`;
                    } else if (product.stock > 0 && product.is_active) {
                        this.query = '';
                        this.addProduct(product);
                        this.notice = `${product.name} berhasil disimpan dan ditambahkan ke keranjang.`;
                    } else {
                        this.notice = `${product.name} berhasil disimpan, tetapi stoknya masih kosong.`;
                    }

                    this.productForm = { name: '', category: '', price: '', stock: 1, is_active: true };
                } catch (error) {
                    this.productError = 'Tidak dapat menyimpan barang. Periksa koneksi lalu coba lagi.';
                } finally {
                    this.savingProduct = false;
                }
            },
            async deleteProduct(product) {
                if (this.savingProduct || !window.confirm(`Hapus barang "${product.name}" dari inventaris?`)) return;

                this.savingProduct = true;
                this.productError = '';

                try {
                    const response = await fetch(@json(url('/products')) + '/' + product.id, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                    const result = await response.json();

                    if (!response.ok) {
                        this.productError = result.message || 'Barang gagal dihapus.';
                        return;
                    }

                    this.products = this.products.filter((item) => item.id !== product.id);
                    const cartItem = this.items.find((item) => item.id === product.id);
                    if (cartItem) this.removeProduct(cartItem);
                    this.notice = `${product.name} berhasil dihapus.`;
                } catch (error) {
                    this.productError = 'Tidak dapat menghapus barang. Periksa koneksi lalu coba lagi.';
                } finally {
                    this.savingProduct = false;
                }
            },
            addFirstResult() {
                const product = this.filteredProducts[0];

                if (product) {
                    this.addProduct(product);
                } else {
                    this.errorMessage = 'Barang tidak ditemukan atau stok kosong.';
                }
            },
            addProduct(product) {
                const existing = this.items.find((item) => item.id === product.id);

                if (existing && existing.quantity >= Number(product.stock)) {
                    this.errorMessage = `Stok ${product.name} tidak mencukupi.`;
                    return;
                }

                if (existing) {
                    existing.quantity += 1;
                } else {
                    this.items.push({ ...product, price: Number(product.price), quantity: 1 });
                }

                this.query = '';
                this.showResults = false;
                this.errorMessage = '';
                this.notice = '';
            },
            increaseQuantity(item) {
                if (item.quantity >= Number(item.stock)) {
                    this.errorMessage = `Stok ${item.name} tidak mencukupi.`;
                    return;
                }

                item.quantity += 1;
                this.errorMessage = '';
            },
            decreaseQuantity(item) {
                if (item.quantity > 1) {
                    item.quantity -= 1;
                } else {
                    this.removeProduct(item);
                }
            },
            removeProduct(item) {
                this.items = this.items.filter((entry) => entry.id !== item.id);
            },
            holdSale() {
                if (this.items.length === 0) {
                    this.errorMessage = 'Tambahkan barang sebelum menahan transaksi.';
                    return;
                }

                localStorage.setItem('minimarket-held-sale', JSON.stringify({
                    items: this.items,
                    customer: this.customer,
                    memberNumber: this.memberNumber,
                    discountPercent: this.discountPercent,
                    discountAmount: this.discountAmount,
                    taxPercent: this.taxPercent,
                    otherFees: this.otherFees,
                    paymentMethod: this.paymentMethod,
                }));
                this.startNewSale();
                this.notice = 'Transaksi ditahan dan dapat dilanjutkan kembali.';
            },
            resumeSale() {
                const heldSale = JSON.parse(localStorage.getItem('minimarket-held-sale') || 'null');

                if (!heldSale) return;

                if (this.items.length > 0 && !window.confirm('Transaksi saat ini akan diganti. Lanjutkan?')) return;

                Object.assign(this, heldSale);
                localStorage.removeItem('minimarket-held-sale');
                this.paidAmount = this.total;
                this.errorMessage = '';
                this.notice = 'Transaksi yang ditahan dilanjutkan.';
            },
            cancelSale() {
                if (this.items.length > 0 && !window.confirm('Batalkan transaksi ini?')) return;

                this.startNewSale();
                this.notice = 'Transaksi dibatalkan.';
            },
            pay() {
                if (this.items.length === 0) return;

                if ((Number(this.paidAmount) || 0) < this.total) {
                    this.errorMessage = 'Uang dibayar belum mencukupi.';
                    return;
                }

                const date = new Date();
                this.receipt = {
                    transactionNumber: this.transactionNumber,
                    date: date.toLocaleString('id-ID'),
                    customer: this.customer === 'Member' && this.memberNumber ? this.memberNumber : this.customer,
                    items: this.items.map((item) => ({ ...item })),
                    subtotal: this.subtotal,
                    discount: this.discount,
                    tax: this.tax,
                    otherFees: Number(this.otherFees) || 0,
                    total: this.total,
                    paymentMethod: this.paymentMethod,
                    paidAmount: Number(this.paidAmount) || 0,
                    change: this.change,
                };
                this.errorMessage = '';
                window.setTimeout(() => window.print(), 50);
            },
            startNewSale() {
                this.items = [];
                this.customer = 'Umum';
                this.memberNumber = '';
                this.discountPercent = 0;
                this.discountAmount = 0;
                this.taxPercent = 0;
                this.otherFees = 0;
                this.paidAmount = 0;
                this.paymentMethod = 'Tunai';
                this.receipt = null;
                this.transactionNumber = `TRX-${new Date().toISOString().slice(2, 10).replaceAll('-', '')}-${Math.floor(1000 + Math.random() * 9000)}`;
                this.errorMessage = '';
            },
        });
    </script>
    </div>
</body>
</html>
