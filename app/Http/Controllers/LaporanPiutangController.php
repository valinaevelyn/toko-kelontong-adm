<?php

namespace App\Http\Controllers;

use App\Models\LaporanPiutang;
use App\Models\Pembelian;
use App\Models\Penjualan;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanPiutangController extends Controller
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

        // Query untuk laporan piutang, hanya dari penjualan
        $laporanPiutang =
            Penjualan::whereIn('metode', ['CEK', 'KREDIT'])
                ->where('status', 'BELUM LUNAS')
                ->select(
                    'tanggal_penjualan as tanggal',
                    'nama_pembeli as nama',
                    DB::raw("CASE metode WHEN 'KREDIT' THEN 'KREDIT' WHEN 'CEK' THEN 'CEK' ELSE 'LAINNYA' END as keterangan"),
                    'total_harga_akhir as jumlah_piutang',
                    'kode_cek',
                    'status',
                    'tanggal_cair'
                )
                ->whereNull('tanggal_cair');  // Piutang yang belum dibayar

        if ($year && $month) {
            $laporanPiutang = $laporanPiutang->whereYear('tanggal_penjualan', $year)
                ->whereMonth('tanggal_penjualan', $month);
        }

        if (!empty($tanggalFilter)) {
            $laporanPiutang = $laporanPiutang->whereDate('tanggal_penjualan', $tanggalFilter);
        }

        $laporanPiutang = $laporanPiutang->orderBy('tanggal_penjualan')
            ->get();

        // Tentukan status keterlambatan berdasarkan tanggal jatuh tempo dan tanggal cair
        foreach ($laporanPiutang as $item) {
            $tanggalPenjualan = Carbon::parse($item->tanggal); // tanggal terbit
            $jatuhTempo = $tanggalPenjualan->copy()->addDays(14);
            $item->jatuh_tempo = $jatuhTempo;

            if (!is_null($item->tanggal_cair)) {
                // Jika sudah ada tanggal cair, status terlambat harus "Sudah lunas"
                $item->status_terlambat = 'Sudah lunas';
            } else {
                // Jika belum cair, hitung keterlambatannya
                if (now()->greaterThan($jatuhTempo)) {
                    // Gunakan round() atau floor() untuk membulatkan hasil
                    $selisih = now()->diffInDays($jatuhTempo);
                    // Gunakan round() atau floor() di sini:
                    $item->status_terlambat = abs(round($selisih));  // Membulatkan ke angka terdekat
                } else {
                    $item->status_terlambat = 'Belum jatuh tempo';
                }
            }
        }

        return view('laporan.piutang', compact('laporanPiutang', 'bulan', 'tanggalFilter'));
    }

    public function storePiutang(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama' => 'required|string',
            'keterangan' => 'required|string',
            'jumlah_piutang' => 'required|integer',
            'jatuh_tempo' => 'required|date',
        ]);

        // Tentukan jatuh tempo 14 hari setelah tanggal penjualan
        $jatuhTempo = Carbon::parse($request->tanggal)->addDays(14);

        // Tanggal cair adalah tanggal cek diterbitkan
        $tanggalCair = $request->tanggal_cair; // Misalnya tanggal cair yang dikirim di form


        LaporanPiutang::create([
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'jumlah_piutang' => $request->jumlah_piutang,
            'jatuh_tempo' => $jatuhTempo,  // Jatuh tempo 14 hari setelah tanggal penjualan
            'tanggal_cair' => $tanggalCair,  // Tanggal cair adalah tanggal cek diterbitkan
        ]);

        return redirect()->route('laporan.piutang')->with('success', 'Piutang berhasil ditambahkan!');
    }

}
