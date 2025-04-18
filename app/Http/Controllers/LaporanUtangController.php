<?php

namespace App\Http\Controllers;

use App\Models\LaporanUtang;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;

class LaporanUtangController extends Controller
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

        // Query untuk laporan utang, hanya dari pembelian
        $laporanUtang =
            Pembelian::whereIn('metode', ['CEK', 'KREDIT'])
                ->where('status', 'BELUM LUNAS')
                ->select(
                    'tanggal_pembelian as tanggal',
                    'nama_supplier as nama',
                    DB::raw("CASE metode WHEN 'KREDIT' THEN 'KREDIT' WHEN 'CEK' THEN 'CEK' ELSE 'LAINNYA' END as keterangan"),
                    'total_harga as jumlah_utang',
                    'kode_cek',
                    'status',
                    'tanggal_cair'
                )
                ->whereNull('tanggal_cair');  // Utang yang belum dibayar

        if ($year && $month) {
            $laporanUtang = $laporanUtang->whereYear('tanggal_pembelian', $year)
                ->whereMonth('tanggal_pembelian', $month);
        }

        if (!empty($tanggalFilter)) {
            $laporanUtang = $laporanUtang->whereDate('tanggal_pembelian', $tanggalFilter);
        }

        $laporanUtang = $laporanUtang->orderBy('tanggal_pembelian')
            ->get();

        // Tentukan status keterlambatan berdasarkan tanggal jatuh tempo dan tanggal cair
        foreach ($laporanUtang as $item) {
            $tanggalPembelian = Carbon::parse($item->tanggal); // tanggal terbit
            $jatuhTempo = $tanggalPembelian->copy()->addDays(14);
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

        return view('laporan.utang', compact('laporanUtang', 'bulan', 'tanggalFilter'));
    }

    public function storeUtang(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama' => 'required|string',
            'keterangan' => 'required|string',
            'jumlah_utang' => 'required|integer',
            'jatuh_tempo' => 'required|date',
        ]);

        // Tentukan jatuh tempo 14 hari setelah tanggal pembelian
        $jatuhTempo = Carbon::parse($request->tanggal)->addDays(14);

        // Tanggal cair adalah tanggal cek diterbitkan
        $tanggalCair = $request->tanggal_cair; // Misalnya tanggal cair yang dikirim di form


        LaporanUtang::create([
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'jumlah_utang' => $request->jumlah_utang,
            'jatuh_tempo' => $jatuhTempo,  // Jatuh tempo 14 hari setelah tanggal pembelian
            'tanggal_cair' => $tanggalCair,  // Tanggal cair adalah tanggal cek diterbitkan
        ]);

        return redirect()->route('laporan.utang')->with('success', 'Utang berhasil ditambahkan!');
    }

}
