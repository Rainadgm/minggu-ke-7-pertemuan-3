<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Pengguna | MiniMarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">MiniMarket Admin</a>
        <a class="btn btn-outline-light btn-sm" href="{{ route('admin.dashboard') }}">Kembali ke dashboard</a>
    </div>
</nav>
<main class="container py-4 py-lg-5">
    <header class="mb-4">
        <p class="text-success text-uppercase fw-bold mb-1">Manajemen akses</p>
        <h1 class="h2">Data Pengguna</h1>
        <p class="text-secondary mb-0">Buat dan kelola akun admin serta kasir. Password disimpan dalam bentuk hash.</p>
    </header>

    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Tambah akun</h2>
            <form action="{{ route('admin.users.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label" for="new-name">Nama</label>
                    <input class="form-control" id="new-name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="new-email">Email</label>
                    <input class="form-control" id="new-email" name="email" type="email" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="new-role">Role</label>
                    <select class="form-select" id="new-role" name="role" required>
                        <option value="kasir" @selected(old('role', 'kasir') === 'kasir')>Kasir</option>
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="new-password">Password</label>
                    <input class="form-control" id="new-password" name="password" type="password" minlength="8" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="new-password-confirmation">Ulangi password</label>
                    <input class="form-control" id="new-password-confirmation" name="password_confirmation" type="password" minlength="8" required>
                </div>
                <div class="col-12"><button class="btn btn-success" type="submit">Buat akun</button></div>
            </form>
        </div>
    </section>

    <section class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="d-flex align-items-center justify-content-between p-4">
                <h2 class="h5 mb-0">Akun terdaftar</h2>
                <span class="badge text-bg-primary">{{ $adminCount }} admin</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th class="ps-4">Pengguna</th><th>Role</th><th>Ubah password</th><th class="text-end pe-4">Aksi</th></tr></thead>
                    <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="ps-4">
                                <form id="update-user-{{ $user->id }}" action="{{ route('admin.users.update', $user) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <label class="visually-hidden" for="name-{{ $user->id }}">Nama</label>
                                    <input class="form-control form-control-sm mb-2" id="name-{{ $user->id }}" name="name" value="{{ $user->name }}" required>
                                    <label class="visually-hidden" for="email-{{ $user->id }}">Email</label>
                                    <input class="form-control form-control-sm" id="email-{{ $user->id }}" name="email" type="email" value="{{ $user->email }}" required>
                                </form>
                            </td>
                            <td>
                                <label class="visually-hidden" for="role-{{ $user->id }}">Role</label>
                                <select class="form-select form-select-sm" name="role" id="role-{{ $user->id }}" form="update-user-{{ $user->id }}">
                                    <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    <option value="kasir" @selected($user->role === 'kasir')>Kasir</option>
                                </select>
                            </td>
                            <td>
                                <label class="visually-hidden" for="password-{{ $user->id }}">Password baru</label>
                                <input class="form-control form-control-sm mb-2" id="password-{{ $user->id }}" name="password" type="password" minlength="8" placeholder="Kosongkan jika tetap" form="update-user-{{ $user->id }}">
                                <label class="visually-hidden" for="password-confirmation-{{ $user->id }}">Ulangi password baru</label>
                                <input class="form-control form-control-sm" id="password-confirmation-{{ $user->id }}" name="password_confirmation" type="password" minlength="8" placeholder="Ulangi password" form="update-user-{{ $user->id }}">
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    <button class="btn btn-sm btn-primary" type="submit" form="update-user-{{ $user->id }}">Simpan</button>
                                    @unless ($user->is(auth()->user()))
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-5 text-center text-secondary">Belum ada akun pengguna.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $users->links() }}</div>
        </div>
    </section>
</main>
</body>
</html>
