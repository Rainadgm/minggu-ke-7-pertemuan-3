<?php

use App\Models\User;

test('cashier can open the cashier dashboard', function () {
    $cashier = User::factory()->create(['role' => 'kasir']);

    $this->actingAs($cashier)
        ->get(route('kasir.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Kasir')
        ->assertSee('Pilih barang')
        ->assertSee('Keranjang belanja')
        ->assertSee('Checkout')
        ->assertSee('Konfirmasi Pembayaran')
        ->assertSee('Jumlah item')
        ->assertSee('Konfirmasi &amp; Bayar', false)
        ->assertSee('Pembayaran Berhasil')
        ->assertSee('No. Invoice')
        ->assertSee('Selesai Tanpa Cetak')
        ->assertSee('Cetak Struk')
        ->assertSee($cashier->name);
});

test('admin cannot open the cashier dashboard', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('kasir.dashboard'))
        ->assertForbidden();
});
