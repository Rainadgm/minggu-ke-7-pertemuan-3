<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

test('admin middleware redirects guests to login', function () {
    $this->get(route('admin'))->assertRedirect(route('login'));
});

test('admin middleware forbids cashiers', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin'))
        ->assertForbidden()
        ->assertSee('ANDA TIDAK MEMILIKI AKSES.');
});

test('admin middleware allows administrators', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin'))
        ->assertOk()
        ->assertSee('Halaman Admin');
});

test('admin middleware returns json for unauthorized json requests', function () {
    $this->actingAs(User::factory()->create())
        ->getJson(route('admin'))
        ->assertForbidden()
        ->assertExactJson(['message' => 'Akses ditolak!']);
});

test('admin alias defaults to the admin role', function () {
    Route::get('/testing/admin-alias', fn () => response()->noContent())
        ->middleware('admin');

    $this->actingAs(User::factory()->admin()->create())
        ->get('/testing/admin-alias')
        ->assertNoContent();
});

test('role middleware accepts a role parameter', function () {
    Route::get('/testing/cashier-only', fn () => response()->noContent())
        ->middleware('cek.role:cashier');

    $this->actingAs(User::factory()->create())
        ->get('/testing/cashier-only')
        ->assertNoContent();
});

test('role middleware logs the completed request', function () {
    Log::spy();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin', ['source' => 'test']))
        ->assertOk();

    Log::shouldHaveReceived('info')->withArgs(fn ($message, $context) =>
        $message === 'Request selesai'
        && str_ends_with($context['url'], '/admin?source=test')
    );
});