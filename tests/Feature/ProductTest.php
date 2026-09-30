<?php

use App\Models\Product;
use App\Models\User;

test('product index redirects to cashier', function () {
    Product::factory()->create([
        'name' => 'Laptop Gaming',
        'price' => 12000000,
        'stock' => 5,
        'is_active' => true,
    ]);

    $response = $this->actingAs(User::factory()->create())->get('/products');

    $response->assertRedirect(route('dashboard'));
});

test('product creation route redirects back to cashier', function () {
    $response = $this->actingAs(User::factory()->create())->get('/products/create');

    $response->assertRedirect(route('dashboard'));
});

test('home redirects guests to login and users to the cashier', function () {
    $guestResponse = $this->get('/');
    $guestResponse->assertRedirect(route('dashboard'));
    $this->get(route('dashboard'))->assertRedirect(route('login'));

    $userResponse = $this->actingAs(User::factory()->create())->get('/');
    $userResponse->assertRedirect(route('dashboard'));
});

test('cashier dashboard loads active and inactive products for inventory management', function () {
    $activeProduct = Product::factory()->create([
        'name' => 'Beras Kasir',
        'is_active' => true,
        'stock' => 12,
    ]);
    Product::factory()->create([
        'name' => 'Produk Nonaktif',
        'is_active' => false,
    ]);

    $response = $this->actingAs(User::factory()->create())->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Keranjang Belanja');
    $response->assertSee('Barang Baru');
    $response->assertViewHas('products', fn ($products) => $products->contains('id', $activeProduct->id)
        && $products->count() === 2);
});

test('a product can be created from the cashier without a page redirect', function () {
    $response = $this->actingAs(User::factory()->create())->postJson(route('products.store'), [
        'name' => 'Barang dari Kasir',
        'category' => 'Makanan',
        'price' => 15000,
        'stock' => 6,
        'is_active' => true,
    ]);

    $response->assertCreated()
        ->assertJsonPath('product.name', 'Barang dari Kasir')
        ->assertJsonPath('product.stock', 6);

    $this->assertDatabaseHas('products', [
        'name' => 'Barang dari Kasir',
        'is_active' => true,
    ]);
});

test('a product can be updated from the cashier without a page redirect', function () {
    $product = Product::factory()->create(['name' => 'Produk Lama']);

    $response = $this->actingAs(User::factory()->create())->putJson(route('products.update', $product), [
        'name' => 'Produk Baru',
        'category' => 'Minuman',
        'price' => 20000,
        'stock' => 8,
        'is_active' => true,
    ]);

    $response->assertOk()
        ->assertJsonPath('product.name', 'Produk Baru')
        ->assertJsonPath('product.stock', 8);

    $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Produk Baru']);
});

test('a product can be deleted from the cashier without a page redirect', function () {
    $product = Product::factory()->create();

    $response = $this->actingAs(User::factory()->create())->deleteJson(route('products.destroy', $product));

    $response->assertOk()->assertJsonPath('deleted', true);
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});
