<?php

namespace App\Services;

use App\Models\CashierTransaction;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use RuntimeException;

class WindowsEscposReceipt
{
    /**
     * @return array<int, array{name: string, share_name: string}>
     */
    public function availablePrinters(): array
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            throw new RuntimeException('Pemilihan printer ESC/POS saat ini hanya didukung pada Windows.');
        }

        $script = '$ErrorActionPreference = "Stop"; [Console]::OutputEncoding = [System.Text.UTF8Encoding]::new(); Get-Printer -ErrorAction Stop | Where-Object { $_.Shared -and $_.ShareName } | Select-Object Name, ShareName | ConvertTo-Json -Compress';
        $process = proc_open(
            ['powershell.exe', '-NoLogo', '-NoProfile', '-NonInteractive', '-Command', $script],
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes
        );

        if (! is_resource($process)) {
            throw new RuntimeException('Tidak dapat menjalankan PowerShell untuk mencari printer Windows.');
        }

        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new RuntimeException(trim($error) ?: 'Windows gagal mengambil daftar printer.');
        }

        if (trim($output) === '') {
            return [];
        }

        $printers = json_decode($output, true, flags: JSON_THROW_ON_ERROR);

        if (! is_array($printers)) {
            throw new RuntimeException('Windows mengembalikan daftar printer dengan format yang tidak valid.');
        }

        if (isset($printers['Name'])) {
            $printers = [$printers];
        }

        return array_values(array_map(static function (array $printer): array {
            if (! is_string($printer['Name'] ?? null) || ! is_string($printer['ShareName'] ?? null)) {
                throw new RuntimeException('Windows mengembalikan informasi printer yang tidak lengkap.');
            }

            return [
                'name' => $printer['Name'],
                'share_name' => $printer['ShareName'],
            ];
        }, $printers));
    }

    public function printTransaction(string $shareName, CashierTransaction $transaction): void
    {
        $printer = new Printer(
            new WindowsPrintConnector($shareName),
            CapabilityProfile::load('simple')
        );

        try {
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("MiniMarket\n");
            $printer->setEmphasis(false);
            $printer->text("STRUK PEMBELIAN\n");
            $printer->text(str_repeat('-', 32)."\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("No: {$transaction->transaction_code}\n");
            $printer->text('Tanggal: '.$transaction->created_at->format('d/m/Y H:i:s')."\n");
            $printer->text('Kasir: '.($transaction->cashier?->name ?? '-')."\n");
            $printer->text(str_repeat('-', 32)."\n");

            foreach ($transaction->items as $item) {
                $printer->text($item->product_name."\n");
                $printer->text("{$item->quantity} x Rp ".number_format((float) $item->unit_price, 0, ',', '.'));
                $printer->text(' = Rp '.number_format((float) $item->line_total, 0, ',', '.')."\n");
            }

            $printer->text(str_repeat('-', 32)."\n");
            $printer->setEmphasis(true);
            $printer->text('TOTAL: Rp '.number_format((float) $transaction->total_amount, 0, ',', '.')."\n");
            $printer->setEmphasis(false);
            $printer->text('Tunai: Rp '.number_format((float) $transaction->cash_received, 0, ',', '.')."\n");
            $printer->text('Kembali: Rp '.number_format((float) $transaction->change_amount, 0, ',', '.')."\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->feed(1);
            $printer->text("Terima kasih\n");
            $printer->feed(2);
            $printer->cut();
        } finally {
            $printer->close();
        }
    }
}
