<?php

namespace App\Http\Controllers;

use App\Models\LaporanKas;
use Illuminate\Http\Request;

class LaporanKasController extends Controller
{
    public function index(Request $request)
    {
        $laporanKasUtama = \DB::table('laporan_kas')
            ->select('tanggal', 'kas_masuk', 'kas_keluar', 'keterangan')
            ->get();

        // Ambil transaksi penjualan yang sudah lunas (sebagai kas_masuk)
        $penjualanLunas = \DB::table('penjualans')
            ->where('status', 'LUNAS')
            ->select('tanggal_penjualan as tanggal', 'total_harga_akhir as kas_masuk', \DB::raw('NULL as kas_keluar'), \DB::raw("'Penjualan' as keterangan"))
            ->get();

        // Ambil transaksi pembelian yang sudah lunas (sebagai kas_keluar)
        $pembelianLunas = \DB::table('pembelians')
            ->where('status', 'LUNAS')
            ->select('tanggal_pembelian as tanggal', \DB::raw('NULL as kas_masuk'), 'total_harga as kas_keluar', \DB::raw("'Pembelian' as keterangan"))
            ->get();


        // Menggabungkan semua transaksi ke dalam laporan kas
        $laporanKas = $laporanKasUtama->concat($penjualanLunas)->concat($pembelianLunas);

        // Mengurutkan laporan kas berdasarkan tanggal
        $laporanKas = $laporanKas->sortBy('tanggal')->values();

        return view('laporan.kas', compact('laporanKas'));
    }

    public function storeBiaya(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'kas_masuk' => 'nullable|integer',
            'kas_keluar' => 'nullable|integer',
        ]);

        $saldoTerakhir = LaporanKas::orderBy('tanggal', 'desc')->first()->saldo ?? 0;
        $kasMasuk = $request->kas_masuk ?? 0;
        $kasKeluar = $request->kas_keluar ?? 0;
        $saldoBaru = $saldoTerakhir + $kasMasuk - $kasKeluar;

        LaporanKas::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'kas_masuk' => $kasMasuk,
            'kas_keluar' => $kasKeluar,
            'saldo' => $saldoBaru,
        ]);

        return redirect()->route('laporan.kas')->with('success', 'Biaya berhasil ditambahkan!');
    }

}
