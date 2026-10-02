<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| HALAMAN UTAMA
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('auth.login');
})->name('login');


/*
|--------------------------------------------------------------------------
| PROSES LOGIN
|--------------------------------------------------------------------------
*/

Route::post('/login', function (Request $request) {

    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if (Auth::user()->role == 'admin') {
            return redirect()->route('admin.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | KASIR
        |--------------------------------------------------------------------------
        */

        if (Auth::user()->role == 'kasir') {
            return redirect()->route('kasir.dashboard');
        }

        Auth::logout();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Role pengguna tidak valid.'
            ]);
    }

    return back()
        ->withErrors([
            'email' => 'Email atau password salah.'
        ])
        ->onlyInput('email');

})->name('login.process');


/*
|--------------------------------------------------------------------------
| HALAMAN ADMIN
|--------------------------------------------------------------------------
|
| URL: /admin
|
| Hanya role admin yang boleh masuk.
|
*/

Route::get('/admin', function () {

    return view('admin.dashboard');

})->middleware(['auth', 'admin'])
  ->name('admin');


/*
|--------------------------------------------------------------------------
| DASHBOARD ADMIN
|--------------------------------------------------------------------------
|
| URL: /admin/dashboard
|
*/

Route::get('/admin/dashboard', function () {

    return view('admin.dashboard');

})->middleware(['auth', 'admin'])
  ->name('admin.dashboard');


/*
|--------------------------------------------------------------------------
| HALAMAN KASIR
|--------------------------------------------------------------------------
|
| URL: /kasir
|
| Hanya role kasir yang boleh masuk.
|
*/

Route::get('/kasir', function () {

    return view('kasir.dashboard');

})->middleware(['auth', 'kasir'])
  ->name('kasir');


/*
|--------------------------------------------------------------------------
| DASHBOARD KASIR
|--------------------------------------------------------------------------
|
| URL: /kasir/dashboard
|
*/

Route::get('/kasir/dashboard', function () {

    return view('kasir.dashboard');

})->middleware(['auth', 'kasir'])
  ->name('kasir.dashboard');


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', function (Request $request) {

    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect()->route('login');

})->middleware('auth')->name('logout');