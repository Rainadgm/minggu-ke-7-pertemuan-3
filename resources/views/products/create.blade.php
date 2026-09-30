@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Tambah Produk</h1>
                <p class="text-sm text-gray-500">Isi data produk baru</p>
            </div>
            <a href="{{ route('products.index') }}"
               class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Kembali
            </a>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Nama Produk</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                               class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="mb-1 block text-sm font-medium text-gray-700">Kategori</label>
                        <input id="category" name="category" type="text" value="{{ old('category') }}"
                               class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock" class="mb-1 block text-sm font-medium text-gray-700">Stok</label>
                        <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', 0) }}"
                               class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" required>
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="mb-1 block text-sm font-medium text-gray-700">Harga</label>
                        <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price') }}"
                               class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" required>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="image" class="mb-1 block text-sm font-medium text-gray-700">Gambar Produk</label>
                        <input id="image" name="image" type="file" accept="image/*"
                               class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:border-blue-500 focus:outline-none">
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="mb-1 block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea id="description" name="description" rows="4"
                                  class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            Aktifkan produk
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('products.index') }}"
                       class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
