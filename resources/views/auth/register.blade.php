<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register | MiniMarket</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-800 antialiased">
        <div class="min-h-screen">
            <div class="grid min-h-screen lg:grid-cols-2">
                <div class="hidden bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-900 p-10 lg:flex lg:flex-col lg:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-lg font-black text-slate-900">M</div>
                        <div>
                            <p class="text-xl font-black text-white">MiniMarket</p>
                            <p class="text-xs uppercase tracking-[0.24em] text-emerald-200">Fresh & Fast</p>
                        </div>
                    </div>

                    <div class="max-w-md">
                        <p class="text-sm font-bold uppercase tracking-[0.24em] text-emerald-200">Bergabung</p>
                        <h1 class="mt-4 text-4xl font-black leading-tight text-white">Mulai belanja lebih mudah dan lebih hemat.</h1>
                        <p class="mt-5 text-base text-slate-300">
                            Daftar sekarang untuk menikmati promo eksklusif dan pengalaman belanja yang lebih cepat serta aman.
                        </p>
                    </div>

                    <div class="flex items-center gap-4 text-sm text-slate-300">
                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5">Promo harian</span>
                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5">Gratis ongkir</span>
                    </div>
                </div>

                <div class="flex items-center justify-center px-4 py-10 sm:px-6 lg:px-10">
                    <div class="w-full max-w-md rounded-[2rem] border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/70">
                        <div class="text-center">
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-600">Daftar</p>
                            <h2 class="mt-2 text-3xl font-black text-slate-900">Buat akun baru</h2>
                        </div>

                        @if ($errors->any())
                            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                <ul class="list-disc space-y-1 pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
                            @csrf

                            <div>
                                <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama lengkap</label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100" placeholder="Masukkan nama Anda">
                            </div>

                            <div>
                                <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100" placeholder="you@example.com">
                            </div>

                            <div>
                                <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                                <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100" placeholder="Minimal 8 karakter">
                            </div>

                            <div>
                                <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Konfirmasi password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100" placeholder="Ulangi password">
                            </div>

                            <button type="submit" class="w-full rounded-2xl bg-emerald-500 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-400">
                                Daftar sekarang
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-slate-600">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="font-bold text-emerald-600 transition hover:text-emerald-700">Masuk</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
