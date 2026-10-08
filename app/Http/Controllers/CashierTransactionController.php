<?php

namespace App\Http\Controllers;

use App\Models\CashierTransaction;
use App\Models\Product;
use App\Services\WindowsEscposReceipt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class CashierTransactionController extends Controller
{
    public function __construct(private readonly WindowsEscposReceipt $receipt) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'cash_received' => ['required', 'numeric', 'min:0'],
            'printer' => ['nullable', 'string', 'max:255'],
        ]);

        $transaction = DB::transaction(function () use ($validated, $request): CashierTransaction {
            $products = Product::query()
                ->whereIn('id', collect($validated['items'])->pluck('product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $totalCents = 0;
            $lines = [];

            foreach ($validated['items'] as $item) {
                $product = $products->get($item['product_id']);
                $quantity = (int) $item['quantity'];

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => 'Produk sudah tidak aktif. Muat ulang halaman kasir.',
                    ]);
                }

                if ($quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak mencukupi. Stok tersedia: {$product->stock}.",
                    ]);
                }

                $unitPriceCents = (int) round((float) $product->price * 100);
                $lineTotalCents = $unitPriceCents * $quantity;
                $totalCents += $lineTotalCents;

                $lines[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price_cents' => $unitPriceCents,
                    'line_total_cents' => $lineTotalCents,
                ];
            }

            $cashReceivedCents = (int) round((float) $validated['cash_received'] * 100);

            if ($cashReceivedCents < $totalCents) {
                throw ValidationException::withMessages([
                    'cash_received' => 'Jumlah uang pembayaran kurang dari total belanja.',
                ]);
            }

            $transaction = CashierTransaction::create([
                'transaction_code' => 'TRX-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'user_id' => $request->user()->id,
                'total_amount' => number_format($totalCents / 100, 2, '.', ''),
                'cash_received' => number_format($cashReceivedCents / 100, 2, '.', ''),
                'change_amount' => number_format(($cashReceivedCents - $totalCents) / 100, 2, '.', ''),
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];
                $product->decrement('stock', $line['quantity']);

                $transaction->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => number_format($line['unit_price_cents'] / 100, 2, '.', ''),
                    'quantity' => $line['quantity'],
                    'line_total' => number_format($line['line_total_cents'] / 100, 2, '.', ''),
                ]);
            }

            return $transaction->load('items.product:id,stock');
        });

        $receiptStatus = [
            'printed' => false,
            'message' => 'Transaksi tersimpan. Pilih printer lalu gunakan Cetak struk transaksi untuk mencetak struk ini.',
        ];

        if (! empty($validated['printer'])) {
            try {
                $printer = collect($this->receipt->availablePrinters())
                    ->firstWhere('share_name', $validated['printer']);

                if (! $printer) {
                    throw ValidationException::withMessages([
                        'printer' => 'Printer yang dipilih tidak tersedia. Pilih printer lain untuk mencetak struk.',
                    ]);
                }

                $this->receipt->printTransaction(
                    $printer['share_name'],
                    $transaction->load(['cashier', 'items'])
                );

                $receiptStatus = [
                    'printed' => true,
                    'message' => 'Struk transaksi berhasil dikirim ke printer.',
                ];
            } catch (Throwable $exception) {
                report($exception);
                $receiptStatus = [
                    'printed' => false,
                    'message' => 'Transaksi tersimpan, tetapi struk belum tercetak. Pilih printer yang aktif dan coba cetak ulang.',
                ];
            }
        }

        return response()->json([
            'transaction' => [
                'id' => $transaction->id,
                'code' => $transaction->transaction_code,
                'total' => $transaction->total_amount,
                'cash_received' => $transaction->cash_received,
                'change' => $transaction->change_amount,
                'receipt' => $receiptStatus,
                'items' => $transaction->items->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                    'stock_after' => $item->product?->stock ?? 0,
                ]),
            ],
        ], 201);
    }
}
