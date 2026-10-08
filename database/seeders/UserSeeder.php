<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            User::query()
                ->whereIn('email', ['admin@minimarket.test', 'kasir@minimarket.test'])
                ->delete();

            User::create([
                'name' => 'Administrator',
                'email' => 'admin@minimarket.test',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);

            User::create([
                'name' => 'Vera Redji',
                'email' => 'kasir@minimarket.test',
                'password' => Hash::make('admin123'),
                'role' => 'kasir',
            ]);
        });
    }
}
