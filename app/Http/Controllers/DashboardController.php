<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $total_pendapatan = Penjualan::sum('total_harga_akhir');
        $total_pengeluaran = Pembelian::sum('total_harga');
        $keuntungan_bersih = $total_pendapatan - $total_pengeluaran;

        // Penjualan berdasarkan metode
        $penjualan_kas = Penjualan::where('metode', 'CASH')->where('status', 'LUNAS')->sum('total_harga_akhir');
        $penjualan_bank = Penjualan::where('metode', 'TRANSFER')->where('status', 'LUNAS')->sum('total_harga_akhir');

        // Pengeluaran berdasarkan metode (pembelian)
        $pembelian_kas = Pembelian::where('metode', 'CASH')->where('status', 'LUNAS')->sum('total_harga');
        $pembelian_bank = Pembelian::where('metode', 'TRANSFER')->where('status', 'LUNAS')->sum('total_harga');
        $pembelian_piutang = Pembelian::whereNotIn('metode', ['CASH', 'TRANSFER'])->where('status', 'BELUM LUNAS')->sum('total_harga');

        // Mutasi saldo
        $mutasi_dari_kas = DB::table('mutasi_saldo')->where('dari', 'KAS')->sum('jumlah');
        $mutasi_ke_kas = DB::table('mutasi_saldo')->where('ke', 'KAS')->sum('jumlah');
        $mutasi_dari_bank = DB::table('mutasi_saldo')->where('dari', 'BANK')->sum('jumlah');
        $mutasi_ke_bank = DB::table('mutasi_saldo')->where('ke', 'BANK')->sum('jumlah');

        // Saldo akhir setelah dikurangi pembelian
        $saldo_kas = $penjualan_kas - $mutasi_dari_kas + $mutasi_ke_kas - $pembelian_kas;
        $saldo_bank = $penjualan_bank - $mutasi_dari_bank + $mutasi_ke_bank - $pembelian_bank;

        // Piutang = selain CASH dan TRANSFER (penjualan dan pembelian)
        $piutang_penjualan = Penjualan::whereNotIn('metode', ['CASH', 'TRANSFER'])->where('status', 'BELUM LUNAS')->sum('total_harga_akhir');
        $saldo_piutang = $piutang_penjualan - $pembelian_piutang;

        // Transaksi terbaru
        $transaksi_terbaru = DB::table(function ($query) {
            $query->select(
                'tanggal_penjualan as tanggal',
                'total_harga_akhir as jumlah',
                DB::raw("'Penjualan' as kategori"),
                DB::raw("'Masuk' as jenis")
            )
                ->from('penjualans')
                ->unionAll(
                    DB::table('pembelians')->select(
                        'tanggal_pembelian as tanggal',
                        'total_harga as jumlah',
                        DB::raw("'Pembelian' as kategori"),
                        DB::raw("'Keluar' as jenis")
                    )
                );
        }, 'transaksi')
            ->select('kategori', 'jenis', 'tanggal', 'jumlah')
            ->orderByDesc('tanggal')
            ->limit(5)
            ->get();

        return view('index', compact(
            'total_pendapatan',
            'total_pengeluaran',
            'saldo_kas',
            'keuntungan_bersih',
            'saldo_bank',
            'transaksi_terbaru',
            'saldo_piutang'
        ));
    }

    public function transferSaldo(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'dari' => 'required|in:BANK,KAS',
            'ke' => 'required|in:BANK,KAS|different:dari',
            'catatan' => 'nullable|string',
        ]);

        // Hitung saldo aktual dari sumber
        $asal = $request->dari;
        $jumlah = $request->jumlah;

        $saldo_asal = $asal === 'BANK'
            ? Penjualan::where('metode', 'TRANSFER')->where('status', 'LUNAS')->sum('total_harga_akhir')
            - DB::table('mutasi_saldo')->where('dari', 'BANK')->sum('jumlah')
            + DB::table('mutasi_saldo')->where('ke', 'BANK')->sum('jumlah')
            : Penjualan::where('metode', 'CASH')->where('status', 'LUNAS')->sum('total_harga_akhir')
            - DB::table('mutasi_saldo')->where('dari', 'KAS')->sum('jumlah')
            + DB::table('mutasi_saldo')->where('ke', 'KAS')->sum('jumlah');

        if ($jumlah > $saldo_asal) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk transfer.');
        }

        DB::table('mutasi_saldo')->insert([
            'dari' => $asal,
            'ke' => $request->ke,
            'jumlah' => $jumlah,
            'catatan' => $request->catatan ?? 'Transfer antar saldo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Saldo berhasil ditransfer.');
    }
}
