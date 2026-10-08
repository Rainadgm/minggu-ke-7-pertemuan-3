<?php

use App\Models\CashierTransaction;
use App\Models\Product;
use App\Models\User;
use App\Services\WindowsEscposReceipt;

test('cashier can check out selected products and stock is reduced', function () {
    $product = Product::factory()->create([
        'name' => 'Beras Checkout',
        'price' => 12500,
        'stock' => 8,
        'is_active' => true,
    ]);
    $cashier = User::factory()->create(['role' => 'kasir']);

    $this->actingAs($cashier)
        ->postJson(route('kasir.transactions.store'), [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
            'cash_received' => 50000,
        ])
        ->assertCreated()
        ->assertJsonPath('transaction.items.0.name', 'Beras Checkout')
        ->assertJsonPath('transaction.items.0.quantity', 3)
        ->assertJsonPath('transaction.total', '37500.00')
        ->assertJsonPath('transaction.change', '12500.00');

    expect($product->fresh()->stock)->toBe(5)
        ->and(CashierTransaction::count())->toBe(1)
        ->and(CashierTransaction::first()->user_id)->toBe($cashier->id);
    $this->assertDatabaseHas('cashier_transaction_items', [
        'product_id' => $product->id,
        'product_name' => 'Beras Checkout',
        'quantity' => 3,
        'line_total' => '37500.00',
    ]);
});

test('admin dashboard reports completed cashier transactions', function () {
    $product = Product::factory()->create(['price' => 2500, 'stock' => 4]);

    $this->actingAs(User::factory()->create(['role' => 'kasir']))
        ->postJson(route('kasir.transactions.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'cash_received' => 5000,
        ])
        ->assertCreated();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Penjualan Hari Ini')
        ->assertSee('Rp 5.000')
        ->assertSee(CashierTransaction::firstOrFail()->transaction_code);
});

test('checkout prints the actual transaction to the selected printer', function () {
    $product = Product::factory()->create([
        'name' => 'Produk untuk struk',
        'price' => 3000,
        'stock' => 4,
    ]);
    $cashier = User::factory()->create(['role' => 'kasir']);

    $receipt = $this->mock(WindowsEscposReceipt::class);
    $receipt->shouldReceive('availablePrinters')
        ->once()
        ->andReturn([
            ['name' => 'POS80', 'share_name' => 'POS80'],
        ]);
    $receipt->shouldReceive('printTransaction')
        ->once()
        ->with('POS80', Mockery::on(
            fn ($transaction) => $transaction->transaction_code !== null
                && $transaction->items->first()->product_name === 'Produk untuk struk'
                && (int) $transaction->items->first()->quantity === 2
                && $transaction->total_amount === '6000.00'
        ));

    $this->actingAs($cashier)
        ->postJson(route('kasir.transactions.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'cash_received' => 10000,
            'printer' => 'POS80',
        ])
        ->assertCreated()
        ->assertJsonPath('transaction.receipt.printed', true)
        ->assertJsonPath('transaction.receipt.message', 'Struk transaksi berhasil dikirim ke printer.');
});

test('checkout preserves the sale and reports an unavailable selected printer', function () {
    $product = Product::factory()->create(['stock' => 3, 'price' => 1000]);

    $receipt = $this->mock(WindowsEscposReceipt::class);
    $receipt->shouldReceive('availablePrinters')
        ->once()
        ->andReturn([]);
    $receipt->shouldNotReceive('printTransaction');

    $this->actingAs(User::factory()->create(['role' => 'kasir']))
        ->postJson(route('kasir.transactions.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'cash_received' => 1000,
            'printer' => 'POS80',
        ])
        ->assertCreated()
        ->assertJsonPath('transaction.receipt.printed', false)
        ->assertJsonPath('transaction.receipt.message', 'Transaksi tersimpan, tetapi struk belum tercetak. Pilih printer yang aktif dan coba cetak ulang.');

    expect($product->fresh()->stock)->toBe(2)
        ->and(CashierTransaction::count())->toBe(1);
});

test('checkout rejects payment below the total without changing stock', function () {
    $product = Product::factory()->create(['price' => 20000, 'stock' => 5]);

    $this->actingAs(User::factory()->create(['role' => 'kasir']))
        ->postJson(route('kasir.transactions.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'cash_received' => 39999,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('cash_received');

    expect($product->fresh()->stock)->toBe(5)
        ->and(CashierTransaction::count())->toBe(0);
});

test('checkout rejects insufficient stock without partially saving the sale', function () {
    $availableProduct = Product::factory()->create(['stock' => 10]);
    $lowStockProduct = Product::factory()->create(['name' => 'Stok Sedikit', 'stock' => 1]);

    $this->actingAs(User::factory()->create(['role' => 'kasir']))
        ->postJson(route('kasir.transactions.store'), [
            'items' => [
                ['product_id' => $availableProduct->id, 'quantity' => 2],
                ['product_id' => $lowStockProduct->id, 'quantity' => 2],
            ],
            'cash_received' => 1000000,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('items');

    expect($availableProduct->fresh()->stock)->toBe(10)
        ->and($lowStockProduct->fresh()->stock)->toBe(1)
        ->and(CashierTransaction::count())->toBe(0);
});

test('checkout rejects inactive products', function () {
    $product = Product::factory()->create(['is_active' => false]);

    $this->actingAs(User::factory()->create(['role' => 'kasir']))
        ->postJson(route('kasir.transactions.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'cash_received' => 1000000,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('items');

    expect(CashierTransaction::count())->toBe(0);
});

test('admins cannot create cashier transactions', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->postJson(route('kasir.transactions.store'), [
            'items' => [],
            'cash_received' => 0,
        ])
        ->assertForbidden();
});
