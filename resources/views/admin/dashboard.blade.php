<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin | MiniMarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 250px; }
        body { background: #f4f6fb; color: #172033; }
        .admin-sidebar { background: #212529; min-height: 100vh; }
        .admin-sidebar .brand { min-height: 76px; }
        .admin-sidebar a { color: #d9dee7; text-decoration: none; display: block; padding: 12px 18px; border-radius: 8px; }
        .admin-sidebar a:hover, .admin-sidebar a.active { background: #343a40; color: #fff; }
        .admin-main { min-width: 0; }
        .stat-card { border: 0; border-radius: 14px; }
        .section-card { border: 0; border-radius: 14px; }
        @media (min-width: 768px) {
            .admin-sidebar { width: var(--sidebar-width); position: fixed; inset: 0 auto 0 0; }
            .admin-main { margin-left: var(--sidebar-width); }
        }
    </style>
</head>
<body>
<div class="admin-sidebar text-white p-3">
    <a class="brand d-flex align-items-center justify-content-center fs-4 fw-bold text-white" href="{{ route('admin.dashboard') }}">MINIMARKET</a>
    <hr>
    <nav class="d-grid gap-1">
        <a class="active" href="{{ route('admin.dashboard') }}">🏠 Dashboard</a>
        <a href="{{ route('admin.users.index') }}">👤 Data Pengguna</a>
        <a href="{{ route('products.index') }}">📦 Data Produk</a>
        <a href="#laporan">📊 Laporan</a>
        <a href="#pengaturan">⚙️ Pengaturan</a>
    </nav>
    <hr>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="btn btn-danger w-100" type="submit">Keluar</button>
    </form>
</div>

<main class="admin-main p-3 p-lg-4 p-xl-5">
    <header class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-success text-uppercase fw-bold mb-1">Panel pengelolaan</p>
            <h1 class="h2 mb-1">Dashboard Admin</h1>
            <p class="text-secondary mb-0">Kelola pengguna, produk, stok, dan informasi toko.</p>
        </div>
        <span class="badge text-bg-primary px-3 py-2">ADMIN</span>
    </header>

    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

    <section class="card section-card shadow-sm mb-4" aria-labelledby="account-heading">
        <div class="card-body p-4">
            <h2 class="h5" id="account-heading">Informasi akun</h2>
            <hr>
            <div class="row g-3">
                <div class="col-md-4"><strong>Nama:</strong> {{ Auth::user()->name }}</div>
                <div class="col-md-5"><strong>Email:</strong> {{ Auth::user()->email }}</div>
                <div class="col-md-3"><strong>Role:</strong> Admin</div>
            </div>
        </div>
    </section>

    <section class="row g-3 mb-4" aria-label="Ringkasan toko">
        <div class="col-sm-6 col-xl-3">
            <article class="card stat-card shadow-sm h-100"><div class="card-body">
                <p class="text-secondary mb-2">Total Pengguna</p><p class="display-6 fw-semibold mb-1">{{ $userCount }}</p>
                <p class="small text-secondary mb-0">{{ $adminCount }} admin · {{ $cashierCount }} kasir</p>
            </div></article>
        </div>
        <div class="col-sm-6 col-xl-3">
            <article class="card stat-card shadow-sm h-100"><div class="card-body">
                <p class="text-secondary mb-2">Total Produk</p><p class="display-6 fw-semibold mb-1">{{ $productCount }}</p>
                <p class="small text-secondary mb-0">{{ $activeProductCount }} produk aktif</p>
            </div></article>
        </div>
        <div class="col-sm-6 col-xl-3">
            <article class="card stat-card shadow-sm h-100"><div class="card-body">
                <p class="text-secondary mb-2">Stok Menipis</p><p class="display-6 fw-semibold mb-1">{{ $lowStockCount }}</p>
                <p class="small text-secondary mb-0">Produk aktif dengan stok 5 atau kurang</p>
            </div></article>
        </div>
        <div class="col-sm-6 col-xl-3">
            <article class="card stat-card shadow-sm h-100"><div class="card-body d-flex flex-column">
                <p class="text-secondary mb-2">Penjualan Hari Ini</p>
                <p class="h3 fw-semibold mb-1">Rp {{ number_format($todaySalesTotal, 0, ',', '.') }}</p>
                <p class="small text-secondary mb-0">{{ $todayTransactionCount }} transaksi tercatat</p>
            </div></article>
        </div>
    </section>

    <section class="card section-card shadow-sm mb-4" id="laporan">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-1">Laporan transaksi terbaru</h2>
                    <p class="text-secondary mb-0">Penjualan yang dicatat melalui checkout kasir.</p>
                </div>
                <span class="badge text-bg-primary">{{ $todayTransactionCount }} transaksi hari ini</span>
            </div>
            @if ($recentTransactions->isEmpty())
                <p class="text-secondary mb-0">Belum ada transaksi.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Kode</th><th>Kasir</th><th>Waktu</th><th>Barang</th><th class="text-end">Total</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->transaction_code }}</td>
                                    <td>{{ $transaction->cashier?->name ?? 'Akun dihapus' }}</td>
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $transaction->items->sum('quantity') }} item</td>
                                    <td class="text-end">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    <section class="card section-card shadow-sm" id="pengaturan">
        <div class="card-body p-4">
            <h2 class="h5 mb-1">Pengaturan & akses</h2>
            <p class="text-secondary mb-3">Halaman admin dan pengelolaan akun hanya dapat dibuka admin. Kasir hanya dapat membuka dashboard kasir dan printer struk.</p>
            <a class="btn btn-outline-primary" href="{{ route('admin.users.index') }}">Kelola akun admin dan kasir</a>
        </div>
    </section>
</main>
</body>
</html>
