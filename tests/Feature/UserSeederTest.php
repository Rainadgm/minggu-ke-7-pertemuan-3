<?php

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;

test('user seeder replaces only the demo accounts with hashed admin123 passwords', function () {
    $oldAdmin = User::factory()->create([
        'name' => 'Old Administrator',
        'email' => 'admin@minimarket.test',
        'role' => 'admin',
    ]);
    $oldCashier = User::factory()->create([
        'name' => 'Old Cashier',
        'email' => 'kasir@minimarket.test',
        'role' => 'kasir',
    ]);
    $unrelatedUser = User::factory()->create([
        'email' => 'someone@example.test',
    ]);

    app(UserSeeder::class)->run();

    expect(User::query()->find($oldAdmin->id))->toBeNull()
        ->and(User::query()->find($oldCashier->id))->toBeNull()
        ->and(User::query()->find($unrelatedUser->id))->not->toBeNull();

    $admin = User::query()->where('email', 'admin@minimarket.test')->firstOrFail();
    $cashier = User::query()->where('email', 'kasir@minimarket.test')->firstOrFail();

    expect($admin->role)->toBe('admin')
        ->and($cashier->role)->toBe('kasir')
        ->and(Hash::check('admin123', $admin->password))->toBeTrue()
        ->and(Hash::check('admin123', $cashier->password))->toBeTrue()
        ->and($admin->password)->not->toBe('admin123')
        ->and($cashier->password)->not->toBe('admin123');
});
