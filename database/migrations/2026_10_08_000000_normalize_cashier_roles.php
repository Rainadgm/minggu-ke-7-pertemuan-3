<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'cashier')->update(['role' => 'kasir']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('kasir')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('cashier')->change();
        });
    }
};
