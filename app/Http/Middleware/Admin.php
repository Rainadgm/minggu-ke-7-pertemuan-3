<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah pengguna sudah login
        if (!Auth::check()) {

            return redirect()->route('login');

        }

        // Cek apakah role pengguna adalah admin
        if (Auth::user()->role !== 'admin') {

            return response()->view('errors.403', [], 403);

        }

        // Jika role admin, lanjutkan ke halaman tujuan
        return $next($request);
    }
}