@extends('layouts.app')

@section('content')
    <div class="bg-slate-50 py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-amber-600">Produk</p>
                    <h1 class="mt-2 text-3xl font-black text-slate-900">Edit Produk</h1>
                </div>

                <a href="{{ route('products.index') }}" class="inline-flex rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Kembali ke katalog</a>
            </div>

            <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
                <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <p class="text-sm font-black uppercase tracking-[0.18em] text-slate-500">Preview</p>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-emerald-700">Aktif</span>
                    </div>

                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" onerror="this.hidden = true; this.nextElementSibling.hidden = false;" class="h-[360px] w-full rounded-[1.5rem] object-cover">
                        <div hidden class="flex h-[360px] w-full items-center justify-center rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-100 text-4xl font-black text-slate-400">
                            {{ strtoupper(substr($product->name, 0, 1)) }}
                        </div>
                    @else
                        <div class="flex h-[360px] w-full items-center justify-center rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-100 text-4xl font-black text-slate-400">
                            {{ strtoupper(substr($product->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="mt-5">
                        <p class="text-sm text-slate-500">{{ $product->category ?: 'Kategori umum' }}</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-900">{{ $product->name }}</h2>
                        <p class="mt-3 text-2xl font-black text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-5 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama Produk</label>
                                <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="category" class="mb-2 block text-sm font-semibold text-slate-700">Kategori</label>
                                <input id="category" name="category" type="text" value="{{ old('category', $product->category) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                                @error('category')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="stock" class="mb-2 block text-sm font-semibold text-slate-700">Stok</label>
                                <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock) }}" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                                @error('stock')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="price" class="mb-2 block text-sm font-semibold text-slate-700">Harga</label>
                                <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price) }}" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                                @error('price')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="image" class="mb-2 block text-sm font-semibold text-slate-700">Gambar Produk</label>
                                <input id="image" name="image" type="file" accept="image/*" class="w-full rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">

                                @if ($product->image)
                                    <div class="mt-4 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" onerror="this.hidden = true;" class="h-16 w-16 rounded-xl object-cover">
                                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                                            <input type="checkbox" name="remove_image" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                            Hapus gambar
                                        </label>
                                    </div>
                                @endif
                                @error('image')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi</label>
                                <textarea id="description" name="description" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="inline-flex items-center gap-3 text-sm font-semibold text-slate-700">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    Aktifkan produk
                                </label>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 pt-3 sm:flex-row sm:justify-end">
                            <a href="{{ route('products.index') }}" class="inline-flex justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100">Batal</a>
                            <button type="submit" class="inline-flex justify-center rounded-full bg-amber-400 px-6 py-3 text-sm font-black text-slate-900 shadow-md shadow-amber-200 transition hover:bg-amber-300">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
