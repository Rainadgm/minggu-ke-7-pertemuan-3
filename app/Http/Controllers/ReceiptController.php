<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class StrukController extends Controller
{
    public function print()
    {
        try {
            // Ganti "POS-58" dengan nama printer thermal yang terpasang di Windows kamu
            $connector = new WindowsPrintConnector("POS-58");
            $printer = new Printer($connector);

            // Header Struk (Acara 36 - Kopi Kita / Minimarket)
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("KOPI KITA\n");
            $printer->setEmphasis(false);
            $printer->text("Jl. Contoh No. 10\n");
            $printer->text("================================\n");

            // Informasi Waktu & Transaksi
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Tanggal : " . date('d-m-Y') . "\n");
            $printer->text("Waktu   : " . date('H:i') . "\n");
            $printer->text("--------------------------------\n");

            // Daftar Barang (Contoh Item)
            $printer->text("Kopi Susu        2 x 15.000\n");
            $printer->text("Roti Bakar       1 x 10.000\n");
            $printer->text("--------------------------------\n");

            // Rincian Pembayaran
            $printer->text("Subtotal        : Rp40.000\n");
            $printer->text("Diskon          : Rp5.000\n");
            $printer->setEmphasis(true);
            $printer->text("TOTAL           : Rp35.000\n");
            $printer->setEmphasis(false);

            // Footer Struk
            $printer->text("================================\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima Kasih!\n");
            $printer->text("Selamat menikmati kopi Anda\n");

            // Potong Kertas & Tutup Koneksi
            $printer->cut();
            $printer->close();

            return response()->json(['message' => 'Struk berhasil dicetak!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}