<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashier_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('cash_received', 12, 2);
            $table->decimal('change_amount', 12, 2);
            $table->timestamps();
        });

        Schema::create('cashier_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->decimal('unit_price', 12, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_transaction_items');
        Schema::dropIfExists('cashier_transactions');
    }
};
