<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class KasirDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('kasir.dashboard', [
            'products' => Product::where('is_active', true)->latest()->get(),
        ]);
    }
}
