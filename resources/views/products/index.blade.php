@extends('layouts.app')

@section('content')
    <div class="bg-slate-50">
        <section class="bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 text-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-16 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:py-20">
                <div class="flex flex-col justify-center">
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-300">Katalog Produk</p>
                    <h1 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl">Temukan kebutuhan harian terbaik untuk keluarga Anda</h1>
                    <p class="mt-5 max-w-xl text-base text-slate-300">
                        Jelajahi berbagai pilihan produk unggulan, promo harian, dan kebutuhan rumah tangga yang siap sampai di rumah Anda.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="#produk-list" class="inline-flex rounded-full bg-amber-400 px-6 py-3 text-sm font-black text-slate-900 transition hover:bg-amber-300">Lihat Katalog</a>
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex rounded-full border border-white/20 bg-white/5 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10">Kembali ke Dashboard</a>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-[1.8rem] border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                        <p class="text-sm text-slate-300">Kategori</p>
                        <p class="mt-3 text-4xl font-black text-white">24</p>
                        <p class="mt-2 text-sm text-emerald-200">Pilihan produk</p>
                    </div>
                    <div class="rounded-[1.8rem] border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                        <p class="text-sm text-slate-300">Promo</p>
                        <p class="mt-3 text-4xl font-black text-white">35%</p>
                        <p class="mt-2 text-sm text-amber-200">Diskon spesial</p>
                    </div>
                    <div class="rounded-[1.8rem] border border-white/10 bg-white/5 p-5 backdrop-blur-sm sm:col-span-2">
                        <p class="text-sm text-slate-300">Best Seller</p>
                        <div class="mt-5 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-3xl font-black text-white">Beras Premium</p>
                                <p class="mt-2 text-sm text-slate-300">Rp 28.000 / kg</p>
                            </div>
                            <span class="rounded-full bg-amber-400 px-3 py-1 text-xs font-black uppercase text-slate-900">Hot</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div id="produk-list" class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-600">Inventory</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-900">Semua Produk</h2>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-700">
                        <span class="text-lg">+</span>
                        Tambah Produk
                    </a>
                </div>
            </div>

            @if ($products->isEmpty())
                <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-3xl text-emerald-700">+</div>
                    <p class="text-xl font-black text-slate-900">Belum ada produk</p>
                    <p class="mt-2 text-sm text-slate-500">Produk yang ditambahkan akan muncul di sini.</p>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($products as $product)
                        <article class="group overflow-hidden rounded-[1.8rem] border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                            <div class="relative h-64 overflow-hidden bg-gradient-to-br from-slate-200 via-white to-emerald-100">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" onerror="this.hidden = true; this.nextElementSibling.hidden = false;" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                    <div hidden class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-200 via-emerald-50 to-amber-100 text-4xl font-black text-slate-500">
                                        {{ strtoupper(substr($product->name, 0, 1)) }}
                                    </div>
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-200 via-emerald-50 to-amber-100 text-4xl font-black text-slate-500">
                                        {{ strtoupper(substr($product->name, 0, 1)) }}
                                    </div>
                                @endif

                                <div class="absolute left-4 top-4 rounded-full bg-white/80 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-slate-800 shadow-sm">
                                    {{ $product->category ?: 'Umum' }}
                                </div>
                            </div>

                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-xl font-black text-slate-900">{{ $product->name }}</h3>
                                        <p class="mt-1 text-sm text-slate-500">{{ $product->stock }} item tersedia</p>
                                    </div>
                                    @if ($product->is_active)
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-emerald-700">Aktif</span>
                                    @else
                                        <span class="rounded-full bg-slate-200 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-slate-600">Nonaktif</span>
                                    @endif
                                </div>

                                <p class="mt-4 text-2xl font-black text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>

                                <p class="mt-4 line-clamp-3 text-sm leading-6 text-slate-600">
                                    {{ $product->description ?: 'Belum ada deskripsi produk. Silakan cek detail lebih lanjut.' }}
                                </p>

                                <div class="mt-5 flex items-center gap-3">
                                    <a href="{{ route('products.show', $product) }}" class="inline-flex flex-1 items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-700">Detail</a>
                                    <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-100">Edit</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($products->hasPages())
                    <div class="mt-8 rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
