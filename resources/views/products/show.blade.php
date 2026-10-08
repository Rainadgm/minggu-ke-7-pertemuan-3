@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 text-slate-800">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.25em] text-emerald-600">Produk</p>
                    <h1 class="mt-2 text-3xl font-black text-slate-900">Detail Produk</h1>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('products.index') }}" class="inline-flex rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Kembali</a>
                    <a href="{{ route('products.edit', $product) }}" class="inline-flex rounded-full bg-amber-400 px-4 py-2.5 text-sm font-bold text-slate-900 shadow-md shadow-amber-200 transition hover:bg-amber-300">Edit Produk</a>
                </div>
            </div>

            <div class="grid gap-8 lg:grid-cols-[1.05fr_1.2fr]">
                <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-4 shadow-sm">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" onerror="this.hidden = true; this.nextElementSibling.hidden = false;" class="h-[460px] w-full rounded-[1.5rem] object-cover">
                        <div hidden class="flex h-[460px] w-full items-center justify-center rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-100 text-slate-400">
                            Gambar tidak tersedia
                        </div>
                    @else
                        <div class="flex h-[460px] w-full items-center justify-center rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-100 text-slate-400">
                            Tidak ada gambar
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black uppercase tracking-[0.18em] text-emerald-700">{{ $product->category ?? 'Umum' }}</span>
                            @if ($product->is_active)
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">Tersedia</span>
                            @else
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">Tidak aktif</span>
                            @endif
                        </div>

                        <h2 class="mt-5 text-4xl font-black tracking-tight text-slate-900">{{ $product->name }}</h2>

                        <div class="mt-5 flex items-end gap-3">
                            <p class="text-3xl font-black text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="pb-1 text-sm text-slate-500">Sudah termasuk pajak</p>
                        </div>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Stok</p>
                                <p class="mt-2 text-xl font-black text-slate-900">{{ $product->stock }} pcs</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Rating</p>
                                <p class="mt-2 text-xl font-black text-slate-900">4.9 / 5</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <button class="inline-flex rounded-full bg-slate-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-slate-700">Tambah ke Keranjang</button>
                            <button class="inline-flex rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100">Beli Langsung</button>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-xl font-black text-slate-900">Deskripsi Produk</h3>
                        <p class="mt-4 leading-7 text-slate-600">
                            {{ $product->description ?: 'Belum ada deskripsi untuk produk ini. Silakan hubungi admin untuk informasi lebih lanjut.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
