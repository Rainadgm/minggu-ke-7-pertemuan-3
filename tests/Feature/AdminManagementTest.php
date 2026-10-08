<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('admin dashboard shows live account and product totals', function () {
    User::factory()->create(['role' => 'kasir']);
    Product::factory()->create(['is_active' => true, 'stock' => 3]);
    Product::factory()->create(['is_active' => false]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Admin')
        ->assertSee('Total Pengguna')
        ->assertSee('Total Produk')
        ->assertSee('Stok Menipis')
        ->assertSee('2', false)
        ->assertSee('1', false);
});

test('admin can open the user and product management pages', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Data Pengguna')
        ->assertSee('Tambah akun');

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Manajemen inventaris')
        ->assertSee('Total Produk')
        ->assertDontSee('Katalog Produk')
        ->assertDontSee('35%');
});

test('admin product catalog renders saved product image urls', function () {
    $product = Product::factory()->create([
        'name' => 'Produk bergambar',
        'image' => 'products/contoh.jpg',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('products.index'))
        ->assertOk()
        ->assertSee($product->name)
        ->assertSee(asset('storage/'.$product->image), false);
});

test('admin product list can filter by search and active status', function () {
    Product::factory()->create(['name' => 'Beras Aktif', 'is_active' => true]);
    Product::factory()->create(['name' => 'Beras Nonaktif', 'is_active' => false]);
    Product::factory()->create(['name' => 'Susu Aktif', 'is_active' => true]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('products.index', ['search' => 'Beras', 'status' => 'active']))
        ->assertOk()
        ->assertSee('Beras Aktif')
        ->assertDontSee('Beras Nonaktif')
        ->assertDontSee('Susu Aktif');
});

test('admin can add a product which appears on the cashier dashboard', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('products.store'), [
            'name' => 'Beras Admin',
            'category' => 'Sembako',
            'price' => 18000,
            'stock' => 10,
            'is_active' => true,
        ])
        ->assertRedirect(route('products.index'));

    $product = Product::where('name', 'Beras Admin')->firstOrFail();

    $this->actingAs(User::factory()->create(['role' => 'kasir']))
        ->get(route('kasir.dashboard'))
        ->assertOk()
        ->assertSee('Beras Admin')
        ->assertSee('18.000');

    expect($product->is_active)->toBeTrue();
});

test('cashier dashboard lists active products only', function () {
    Product::factory()->create(['name' => 'Produk Aktif', 'is_active' => true]);
    Product::factory()->create(['name' => 'Produk Nonaktif', 'is_active' => false]);

    $this->actingAs(User::factory()->create(['role' => 'kasir']))
        ->get(route('kasir.dashboard'))
        ->assertOk()
        ->assertSee('Produk Aktif')
        ->assertDontSee('Produk Nonaktif');
});

test('admin can create cashier and admin accounts with hashed passwords', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.users.store'), [
            'name' => 'Kasir Baru',
            'email' => 'kasir.baru@example.test',
            'role' => 'kasir',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])
        ->assertRedirect(route('admin.users.index'));

    $cashier = User::where('email', 'kasir.baru@example.test')->firstOrFail();

    expect($cashier->role)->toBe('kasir')
        ->and(Hash::check('password-baru', $cashier->password))->toBeTrue();
});

test('cashiers cannot access admin user or product management', function () {
    $cashier = User::factory()->create(['role' => 'kasir']);

    $this->actingAs($cashier)
        ->get(route('admin.users.index'))
        ->assertForbidden();

    $this->get(route('products.index'))->assertForbidden();

    $this->post(route('products.store'), [
        'name' => 'Kasir Tidak Boleh Tambah',
        'price' => 1000,
        'stock' => 1,
    ])->assertForbidden();
});

test('admins cannot use the cashier dashboard or printer endpoints', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('kasir.dashboard'))
        ->assertForbidden();

    $this->getJson(route('kasir.printers'))->assertForbidden();
});

test('admin management cannot remove the active admin or last admin account', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $admin))
        ->assertRedirect()
        ->assertSessionHasErrors('user');

    expect(User::where('role', 'admin')->count())->toBe(1);
});

test('admin cannot demote their own account but can delete another admin account', function () {
    $admin = User::factory()->admin()->create();
    $otherAdmin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'kasir',
        ])
        ->assertSessionHasErrors('role');

    expect($admin->fresh()->role)->toBe('admin');

    $this->delete(route('admin.users.destroy', $otherAdmin))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHasNoErrors();

    expect(User::where('role', 'admin')->count())->toBe(1)
        ->and(User::find($otherAdmin->id))->toBeNull();
});
