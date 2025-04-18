<?php

namespace App\Http\Controllers;

use App\Models\LaporanBank;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class LaporanBankController extends Controller
{
    public function index(Request $request)
    {
        // Ambil parameter bulan dari request
        $bulan = $request->input('bulan');
        $tanggalFilter = $request->input('tanggal');

        if (!empty($bulan) && $bulan !== 'ALL') {
            $date = Carbon::createFromFormat('Y-m', $bulan);
            $year = $date->year;
            $month = $date->month;
        } else {
            $year = null;
            $month = null;
        }

        $laporanBankUtama = DB::table('laporan_banks')
            ->select('tanggal', 'nama', 'bank_masuk', 'bank_keluar', 'keterangan');

        if ($year && $month) {
            $laporanBankUtama = $laporanBankUtama
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month);
        }

        if (!empty($tanggalFilter)) {
            $laporanBankUtama = $laporanBankUtama->whereDate('tanggal', $tanggalFilter);
        }

        $penjualanLunas = DB::table('penjualans')
            ->where('status', 'LUNAS')
            ->where('metode', 'TRANSFER')
            ->select(
                'tanggal_penjualan as tanggal',
                'nama_pembeli as nama',
                'total_harga_akhir as bank_masuk',
                DB::raw('NULL as bank_keluar'),
                DB::raw("'Penjualan' as keterangan")
            );

        if ($year && $month) {
            $penjualanLunas = $penjualanLunas
                ->whereYear('tanggal_penjualan', $year)
                ->whereMonth('tanggal_penjualan', $month);
        }

        if (!empty($tanggalFilter)) {
            $penjualanLunas = $penjualanLunas->whereDate('tanggal_penjualan', $tanggalFilter);
        }

        // Query transaksi pembelian yang sudah lunas (sebagai bank_keluar)
        $pembelianLunas = DB::table('pembelians')
            ->where('status', 'LUNAS')
            ->where('metode', 'TRANSFER')
            ->select(
                'tanggal_pembelian as tanggal',
                'nama_supplier as nama',
                DB::raw('NULL as bank_masuk'),
                'total_harga as bank_keluar',
                DB::raw("'Pembelian' as keterangan")
            );

        if ($year && $month) {
            $pembelianLunas = $pembelianLunas
                ->whereYear('tanggal_pembelian', $year)
                ->whereMonth('tanggal_pembelian', $month);
        }

        if (!empty($tanggalFilter)) {
            $pembelianLunas = $pembelianLunas->whereDate('tanggal_pembelian', $tanggalFilter);
        }
        // Gabungkan semua transaksi menggunakan union
        $laporanBank = $laporanBankUtama->union($penjualanLunas)->union($pembelianLunas);

        // Ambil data dan urutkan berdasarkan tanggal
        $laporanBank = $laporanBank->orderBy('tanggal')->get();

        $totalBankMasuk = $laporanBank->sum('bank_masuk');
        $totalBankKeluar = $laporanBank->sum('bank_keluar');
        $saldoAkhir = $totalBankMasuk - $totalBankKeluar;

        return view('laporan.bank', compact('laporanBank', 'bulan', 'tanggalFilter', 'totalBankMasuk', 'totalBankKeluar', 'saldoAkhir'));
    }

    public function storeBiaya(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama' => 'required|string',
            'keterangan' => 'required|string',
            'bank_masuk' => 'nullable|integer',
            'bank_keluar' => 'nullable|integer',
        ]);

        $saldoTerakhir = LaporanBank::orderBy('tanggal', 'desc')->first()->saldo ?? 0;
        $bankMasuk = $request->bank_masuk ?? 0;
        $bankKeluar = $request->bank_keluar ?? 0;
        $saldoBaru = $saldoTerakhir + $bankMasuk - $bankKeluar;

        LaporanBank::create([
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'bank_masuk' => $bankMasuk,
            'bank_keluar' => $bankKeluar,
            'saldo' => $saldoBaru,
        ]);

        return redirect()->route('laporan.bank')->with('success', 'Biaya berhasil ditambahkan!');
    }

}
