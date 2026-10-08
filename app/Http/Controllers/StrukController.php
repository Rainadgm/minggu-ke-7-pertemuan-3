<?php

namespace App\Http\Controllers;

use App\Models\CashierTransaction;
use App\Services\WindowsEscposReceipt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class StrukController extends Controller
{
    public function __construct(private readonly WindowsEscposReceipt $receipt) {}

    public function printers(): JsonResponse
    {
        try {
            return response()->json(['printers' => $this->receipt->availablePrinters()]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Daftar printer tidak dapat dimuat. Pastikan printer sudah terpasang dan dibagikan di Windows.',
            ], 500);
        }
    }

    public function printTransaction(Request $request, CashierTransaction $transaction): JsonResponse
    {
        $validated = $request->validate([
            'printer' => ['required', 'string', 'max:255'],
        ]);

        if ($transaction->user_id !== $request->user()->id) {
            abort(404);
        }

        try {
            $printer = collect($this->receipt->availablePrinters())
                ->firstWhere('share_name', $validated['printer']);

            if ($printer === null) {
                throw ValidationException::withMessages([
                    'printer' => 'Printer yang dipilih tidak tersedia. Muat ulang daftar printer.',
                ]);
            }

            $this->receipt->printTransaction(
                $printer['share_name'],
                $transaction->load(['cashier', 'items'])
            );

            return response()->json(['message' => 'Struk transaksi berhasil dikirim ke printer.']);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Transaksi tersimpan, tetapi struk gagal dicetak. Periksa printer lalu coba cetak ulang.',
            ], 500);
        }
    }
}
