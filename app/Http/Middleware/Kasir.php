<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Kasir
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah pengguna sudah login
        if (!Auth::check()) {

            return redirect()->route('login');

        }

        // Cek role kasir
        if (Auth::user()->role !== 'kasir') {

            return response()->view('errors.403', [], 403);

        }

        // Jika role kasir, lanjutkan
        return $next($request);
    }
}