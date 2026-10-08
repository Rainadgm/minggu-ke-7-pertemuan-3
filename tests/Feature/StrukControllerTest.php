<?php

use App\Models\CashierTransaction;
use App\Models\User;
use App\Services\WindowsEscposReceipt;

test('cashier dashboard exposes the printer selection dialog', function () {
    $this->actingAs(User::factory()->create(['role' => 'kasir']))
        ->get(route('kasir.dashboard'))
        ->assertOk()
        ->assertSee('Pilih printer')
        ->assertSee('Cetak struk transaksi')
        ->assertDontSee('Cetak struk tes');
});

test('cashier dashboard can load shared Windows printers', function () {
    $this->mock(WindowsEscposReceipt::class)
        ->shouldReceive('availablePrinters')
        ->once()
        ->andReturn([
            ['name' => 'Printer Struk', 'share_name' => 'PrinterStruk'],
        ]);

    $this->actingAs(User::factory()->create(['role' => 'kasir']))
        ->getJson(route('kasir.printers'))
        ->assertOk()
        ->assertJsonPath('printers.0.name', 'Printer Struk')
        ->assertJsonPath('printers.0.share_name', 'PrinterStruk');
});

test('cashier can print a saved transaction receipt to an available printer', function () {
    $cashier = User::factory()->create(['role' => 'kasir']);
    $transaction = CashierTransaction::create([
        'transaction_code' => 'TRX-TEST-001',
        'user_id' => $cashier->id,
        'total_amount' => 20000,
        'cash_received' => 25000,
        'change_amount' => 5000,
    ]);

    $receipt = $this->mock(WindowsEscposReceipt::class);
    $receipt->shouldReceive('availablePrinters')
        ->once()
        ->andReturn([
            ['name' => 'Printer Struk', 'share_name' => 'PrinterStruk'],
        ]);
    $receipt->shouldReceive('printTransaction')
        ->once()
        ->with('PrinterStruk', Mockery::on(fn ($printed) => $printed->is($transaction)));

    $this->actingAs($cashier)
        ->postJson(route('kasir.transactions.receipt', $transaction), ['printer' => 'PrinterStruk'])
        ->assertOk()
        ->assertJsonPath('message', 'Struk transaksi berhasil dikirim ke printer.');
});

test('cashier cannot print a transaction to an unlisted printer', function () {
    $cashier = User::factory()->create(['role' => 'kasir']);
    $transaction = CashierTransaction::create([
        'transaction_code' => 'TRX-TEST-002',
        'user_id' => $cashier->id,
        'total_amount' => 20000,
        'cash_received' => 20000,
        'change_amount' => 0,
    ]);

    $receipt = $this->mock(WindowsEscposReceipt::class);
    $receipt->shouldReceive('availablePrinters')
        ->once()
        ->andReturn([
            ['name' => 'Printer Struk', 'share_name' => 'PrinterStruk'],
        ]);
    $receipt->shouldNotReceive('printTransaction');

    $this->actingAs($cashier)
        ->postJson(route('kasir.transactions.receipt', $transaction), ['printer' => 'UnknownPrinter'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('printer');
});

test('cashier cannot print another cashiers transaction', function () {
    $transaction = CashierTransaction::create([
        'transaction_code' => 'TRX-TEST-003',
        'user_id' => User::factory()->create(['role' => 'kasir'])->id,
        'total_amount' => 20000,
        'cash_received' => 20000,
        'change_amount' => 0,
    ]);

    $this->actingAs(User::factory()->create(['role' => 'kasir']))
        ->postJson(route('kasir.transactions.receipt', $transaction), ['printer' => 'PrinterStruk'])
        ->assertNotFound();
});

test('printer endpoints are unavailable to non-cashiers', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->getJson(route('kasir.printers'))
        ->assertForbidden();

    $this->postJson(route('kasir.transactions.store'), [
        'items' => [],
        'cash_received' => 0,
    ])->assertForbidden();
});
