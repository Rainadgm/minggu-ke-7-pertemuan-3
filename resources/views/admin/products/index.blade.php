<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Produk | MiniMarket Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 250px; }
        body { background: #f4f6fb; color: #172033; }
        .admin-sidebar { background: #212529; min-height: 100vh; }
        .admin-sidebar .brand { min-height: 76px; }
        .admin-sidebar a { color: #d9dee7; text-decoration: none; display: block; padding: 12px 18px; border-radius: 8px; }
        .admin-sidebar a:hover, .admin-sidebar a.active { background: #343a40; color: #fff; }
        .admin-main { min-width: 0; }
        .stat-card, .content-card { border: 0; border-radius: 14px; }
        .product-image { width: 56px; height: 56px; object-fit: cover; }
        @media (min-width: 768px) {
            .admin-sidebar { width: var(--sidebar-width); position: fixed; inset: 0 auto 0 0; }
            .admin-main { margin-left: var(--sidebar-width); }
        }
    </style>
</head>
<body>
<aside class="admin-sidebar text-white p-3">
    <a class="brand d-flex align-items-center justify-content-center fs-4 fw-bold text-white" href="{{ route('admin.dashboard') }}">MINIMARKET</a>
    <hr>
    <nav class="d-grid gap-1" aria-label="Navigasi admin">
        <a href="{{ route('admin.dashboard') }}">🏠 Dashboard</a>
        <a href="{{ route('admin.users.index') }}">👤 Data Pengguna</a>
        <a class="active" href="{{ route('products.index') }}">📦 Data Produk</a>
        <a href="{{ route('admin.dashboard') }}#laporan">📊 Laporan</a>
        <a href="{{ route('admin.dashboard') }}#pengaturan">⚙️ Pengaturan</a>
    </nav>
    <hr>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="btn btn-danger w-100" type="submit">Keluar</button>
    </form>
</aside>

<main class="admin-main p-3 p-lg-4 p-xl-5">
    <header class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-success text-uppercase fw-bold mb-1">Manajemen inventaris</p>
            <h1 class="h2 mb-1">Data Produk</h1>
            <p class="text-secondary mb-0">Kelola katalog, harga, stok, gambar, dan status produk toko.</p>
        </div>
        <a class="btn btn-success" href="{{ route('products.create') }}">+ Tambah Produk</a>
    </header>

    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

    <section class="row g-3 mb-4" aria-label="Ringkasan produk">
        <div class="col-sm-4">
            <article class="card stat-card shadow-sm h-100"><div class="card-body">
                <p class="text-secondary mb-2">Total Produk</p>
                <p class="display-6 fw-semibold mb-0">{{ $productCount }}</p>
            </div></article>
        </div>
        <div class="col-sm-4">
            <article class="card stat-card shadow-sm h-100"><div class="card-body">
                <p class="text-secondary mb-2">Produk Aktif</p>
                <p class="display-6 fw-semibold mb-0">{{ $activeProductCount }}</p>
            </div></article>
        </div>
        <div class="col-sm-4">
            <article class="card stat-card shadow-sm h-100"><div class="card-body">
                <p class="text-secondary mb-2">Stok Menipis</p>
                <p class="display-6 fw-semibold mb-0">{{ $lowStockCount }}</p>
                <p class="small text-secondary mb-0">Aktif dengan stok 5 atau kurang</p>
            </div></article>
        </div>
    </section>

    <section class="card content-card shadow-sm">
        <div class="card-body p-3 p-lg-4">
            <form action="{{ route('products.index') }}" method="GET" class="row g-2 mb-4">
                <div class="col-md">
                    <label class="visually-hidden" for="product-search">Cari produk atau kategori</label>
                    <input class="form-control" id="product-search" name="search" value="{{ request('search') }}" placeholder="Cari nama produk atau kategori">
                </div>
                <div class="col-md-3">
                    <label class="visually-hidden" for="product-status">Filter status</label>
                    <select class="form-select" id="product-status" name="status">
                        <option value="">Semua status</option>
                        <option value="active" @selected(request('status') === 'active')>Aktif</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                    </select>
                </div>
                <div class="col-auto"><button class="btn btn-outline-primary" type="submit">Terapkan</button></div>
                @if (request()->filled('search') || request()->filled('status'))
                    <div class="col-auto"><a class="btn btn-outline-secondary" href="{{ route('products.index') }}">Reset</a></div>
                @endif
            </form>

            @if ($products->isEmpty())
                <div class="rounded border border-dashed p-5 text-center">
                    <h2 class="h5">Produk tidak ditemukan</h2>
                    <p class="text-secondary mb-0">Ubah filter pencarian atau tambahkan produk baru.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if ($product->image)
                                                <img class="product-image rounded border" src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" onerror="this.hidden = true; this.nextElementSibling.hidden = false;">
                                                <div hidden class="product-image rounded border bg-light d-flex align-items-center justify-content-center fw-bold text-secondary">{{ strtoupper(substr($product->name, 0, 1)) }}</div>
                                            @else
                                                <div class="product-image rounded border bg-light d-flex align-items-center justify-content-center fw-bold text-secondary">{{ strtoupper(substr($product->name, 0, 1)) }}</div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $product->name }}</div>
                                                <div class="small text-secondary">{{ \Illuminate\Support\Str::limit($product->description ?: 'Tidak ada deskripsi', 55) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->category ?: 'Umum' }}</td>
                                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="{{ $product->stock <= 5 ? 'text-danger fw-semibold' : '' }}">{{ $product->stock }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $product->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('products.edit', $product) }}">Edit</a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $products->links() }}</div>
            @endif
        </div>
    </section>
</main>
</body>
</html>
