<?php

namespace App\Http\Controllers;

use App\Models\LaporanKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanKasController extends Controller
{
    public function index(Request $request)
    {
        // Ambil parameter bulan dari request
        $bulan = $request->input('bulan');


        // Jika filter bulan dipilih, buat variabel filter
        if (!empty($bulan) && $bulan !== 'ALL') {
            $date = Carbon::createFromFormat('Y-m-d', $bulan . '-01');
            $year = $date->year;
            $month = $date->month;
        } else {
            $year = null;
            $month = null;
        }

        // Query dasar untuk laporan kas utama
        $laporanKasUtama = DB::table('laporan_kas')
            ->select('tanggal', 'nama', 'kas_masuk', 'kas_keluar', 'keterangan');

        if ($year && $month) {
            $laporanKasUtama = $laporanKasUtama
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month);
        }


        // Query transaksi penjualan yang sudah lunas (sebagai kas_masuk)
        $penjualanLunas = DB::table('penjualans')
            ->where('status', 'LUNAS')
            ->select(
                'tanggal_penjualan as tanggal',
                'nama_pembeli as nama',
                'total_harga_akhir as kas_masuk',
                DB::raw('NULL as kas_keluar'),
                DB::raw("'Penjualan' as keterangan")
            );

        if ($year && $month) {
            $penjualanLunas = $penjualanLunas
                ->whereYear('tanggal_penjualan', $year)
                ->whereMonth('tanggal_penjualan', $month);
        }

        // Query transaksi pembelian yang sudah lunas (sebagai kas_keluar)
        $pembelianLunas = DB::table('pembelians')
            ->where('status', 'LUNAS')
            ->select(
                'tanggal_pembelian as tanggal',
                'nama_supplier as nama',
                DB::raw('NULL as kas_masuk'),
                'total_harga as kas_keluar',
                DB::raw("'Pembelian' as keterangan")
            );

        if ($year && $month) {
            $pembelianLunas = $pembelianLunas
                ->whereYear('tanggal_pembelian', $year)
                ->whereMonth('tanggal_pembelian', $month);
        }

        // Gabungkan semua transaksi menggunakan union
        $laporanKas = $laporanKasUtama->union($penjualanLunas)->union($pembelianLunas);

        // Ambil data dan urutkan berdasarkan tanggal
        $laporanKas = $laporanKas->orderBy('tanggal')->get();

        return view('laporan.kas', compact('laporanKas', 'bulan'));
    }

    public function storeBiaya(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama' => 'required|string',
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
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'kas_masuk' => $kasMasuk,
            'kas_keluar' => $kasKeluar,
            'saldo' => $saldoBaru,
        ]);

        return redirect()->route('laporan.kas')->with('success', 'Biaya berhasil ditambahkan!');
    }

}
