<?php

namespace App\Http\Controllers;

use App\Models\CashierTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'adminCount' => User::where('role', 'admin')->count(),
            'cashierCount' => User::where('role', 'kasir')->count(),
            'productCount' => Product::count(),
            'activeProductCount' => Product::where('is_active', true)->count(),
            'lowStockCount' => Product::where('is_active', true)->where('stock', '<=', 5)->count(),
            'todayTransactionCount' => CashierTransaction::whereDate('created_at', today())->count(),
            'todaySalesTotal' => CashierTransaction::whereDate('created_at', today())->sum('total_amount'),
            'recentTransactions' => CashierTransaction::with('cashier', 'items')
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }
}
