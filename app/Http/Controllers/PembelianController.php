<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\LaporanUtang;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Str;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pembelian::with('pembelianDetails.item')->latest();

        if ($request->has('status') && in_array($request->status, ['LUNAS', 'BELUM LUNAS'])) {
            $query->where('status', $request->status);
        }

        $pembelians = $query->paginate(10);

        return view('pembelian.index', data: compact('pembelians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::all();
        return view('pembelian.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string',
            'items' => 'required|array',
        ]);

        $tanggal = Carbon::now();
        $prefix = 'F' . $tanggal->format('Ym'); // contoh: F202504
        $randomCode = strtoupper(Str::random(6)); // contoh: 6 karakter acak
        $noFaktur = $prefix . '-' . $randomCode;

        // Buat transaksi pembelian
        $pembelian = Pembelian::create([
            'no_faktur' => $noFaktur,
            'nama_supplier' => $request->nama_supplier,
            'tanggal_pembelian' => now(),
            'total_harga' => 0, // Akan di-update setelah menambahkan detail
            'total_item' => 0, // Akan di-update setelah menambahkan detail
            'total_uang' => 0,
            'kembalian' => 0,
            'metode' => '',
            'status' => 'BELUM LUNAS',
        ]);

        $totalHarga = 0;
        $totalItem = 0;

        // Tambahkan detail item ke pembelian
        foreach ($request->items as $item) {
            $itemData = Item::find($item['id']);


            $pcsFromDus = $item['stock_dus'] * $itemData->dus_in_pcs;
            $pcsFromRcg = $item['stock_rcg'] * $itemData->rcg_in_pcs;
            $totalPcs = $pcsFromDus + $pcsFromRcg + $item['stock_pcs'];

            // $subtotal = $item['jumlah'] * $itemData->harga_satuan;

            // dd($totalPcs, $item['harga_satuan']);

            // $subtotal = $totalPcs * $item['harga_satuan'];

            // dd($subtotal);

            PembelianDetail::create([
                'pembelian_id' => $pembelian->id,
                'item_id' => $item['id'],
                'jumlah_dus' => $item['stock_dus'],
                'jumlah_rcg' => $item['stock_rcg'],
                'jumlah_pcs' => $item['stock_pcs'],
                'jumlah' => $totalPcs,
                'harga_satuan' => $item['harga_satuan'],
            ]);

            // dd()

            // Tambah stok item
            $itemData->increment('stock_dus', $item['stock_dus'] ?? 0);
            $itemData->increment('stock_rcg', $item['stock_rcg'] ?? 0);
            $itemData->increment('stock_pcs', $item['stock_pcs'] ?? 0);
            $totalHarga += $totalPcs * $item['harga_satuan'];
            $totalItem += $totalPcs;
        }

        // Update total harga pembelian
        $pembelian->update([
            'total_harga' => $totalHarga,
            'total_item' => $totalItem
        ]);

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembelian $pembelian)
    {
        return view('pembelian.show', compact('pembelian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembelian $pembelian)
    {
        $items = Item::all();
        return view('pembelian.edit', compact('pembelian', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembelian $pembelian)
    {
        // Hapus detail lama dan kembalikan stok ke item
        foreach ($pembelian->pembelianDetails as $detail) {
            $item = Item::find($detail->item_id);

            $item->decrement('stock_dus', $detail->jumlah_dus);
            $item->decrement('stock_rcg', $detail->jumlah_rcg);
            $item->decrement('stock_pcs', $detail->jumlah_pcs);

            $detail->delete();
        }

        $totalHarga = 0;
        $totalItem = 0;

        foreach ($request->items as $item) {
            $itemData = Item::find($item['id']);

            // Ambil data jumlah dan konversi
            $jumlah_dus = $item['jumlah_dus'] ?? 0;
            $jumlah_rcg = $item['jumlah_rcg'] ?? 0;
            $jumlah_pcs = $item['jumlah_pcs'] ?? 0;
            $dus_in_pcs = $item['dus_in_pcs'] ?? $itemData->dus_in_pcs ?? 0;
            $rcg_in_pcs = $item['rcg_in_pcs'] ?? $itemData->rcg_in_pcs ?? 0;

            // Hitung total PCS
            $totalPcs = ($jumlah_dus * $dus_in_pcs) + ($jumlah_rcg * $rcg_in_pcs) + $jumlah_pcs;

            // Hitung subtotal
            $subtotal = $totalPcs * $item['harga_satuan'];

            // Simpan detail baru
            PembelianDetail::create([
                'pembelian_id' => $pembelian->id,
                'item_id' => $item['id'],
                'jumlah_dus' => $jumlah_dus,
                'jumlah_rcg' => $jumlah_rcg,
                'jumlah_pcs' => $jumlah_pcs,
                'jumlah' => $totalPcs,
                'harga_satuan' => $item['harga_satuan'],
            ]);

            // Update stok item
            $itemData->increment('stock_dus', $jumlah_dus);
            $itemData->increment('stock_rcg', $jumlah_rcg);
            $itemData->increment('stock_pcs', $jumlah_pcs);

            $totalHarga += $subtotal;
            $totalItem += $totalPcs;
        }

        $pembelian->update([
            'total_harga' => $totalHarga,
            'total_item' => $totalItem,
            'nama_supplier' => $request->nama_supplier,
            'tanggal_pembelian' => $request->tanggal_pembelian,
        ]);

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil diperbarui!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembelian $pembelian)
    {
        // Hapus detail pembelian
        foreach ($pembelian->pembelianDetails as $detail) {
            $item = Item::find($detail->item_id);

            $item->decrement('stock_dus', $detail->jumlah_dus);
            $item->decrement('stock_rcg', $detail->jumlah_rcg);
            $item->decrement('stock_pcs', $detail->jumlah_pcs);

            $detail->delete();
        }

        // Hapus laporan utang jika ada
        LaporanUtang::where('pembelian_id', $pembelian->id)->delete();

        // Hapus pembelian
        $pembelian->delete();

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dihapus!');
    }

    public function pelunasan(Request $request, $id)
    {
        $request->validate([
            'jumlah_uang' => 'nullable|required_if:metode_pembayaran,CASH|numeric|min:0',
            'metode_pembayaran' => 'required|in:CASH,KREDIT,CEK,TRANSFER',
            'kode_cek' => 'required_if:metode_pembayaran,CEK|string|nullable',
            'tanggal_cair' => 'required_if:metode_pembayaran,CEK|date|nullable',
        ]);

        $pembelian = Pembelian::findOrFail($id);
        $totalHarga = $pembelian->total_harga;
        $jumlahUang = $request->jumlah_uang;
        $metode = $request->metode_pembayaran;

        if ($metode === 'CASH' && $jumlahUang < $totalHarga) {
            return response()->json(['success' => false, 'message' => 'Jumlah uang kurang!'], 400);
        }

        $kembalian = ($metode === 'CASH') ? $jumlahUang - $totalHarga : 0;

        // $pembelian->status = 'LUNAS';
        // $pembelian->kembalian = $kembalian;
        // $pembelian->total_uang = $metode === 'CASH' ? $jumlahUang : 0;
        // $pembelian->metode = $metode;

        // if ($metode === 'CEK') {
        //     $pembelian->kode_cek = $request->kode_cek;
        //     $pembelian->tanggal_cair = $request->tanggal_cair;
        // }

        if ($metode == 'CASH') {
            $pembelian->total_uang = $jumlahUang;
            $pembelian->status = 'LUNAS';
        }

        $pembelian->kembalian = $kembalian;
        $pembelian->metode = $metode;

        if ($metode == 'TRANSFER') {
            $pembelian->status = 'LUNAS';
        }

        if ($metode == 'CEK') {
            $pembelian->kode_cek = $request->kode_cek;
            $pembelian->tanggal_cair = $request->tanggal_cair;
            $pembelian->status = $request->status_saldo;
        }

        if ($metode == 'KREDIT') {
            $pembelian->status = $request->status_saldo;
        }

        $pembelian->save();

        if ($pembelian->tanggal_cair) {
            $laporanUtang = LaporanUtang::where('pembelian_id', $pembelian->id)->first();

            if ($laporanUtang) {
                $laporanUtang->status_terlambat = 'Sudah lunas';
                $laporanUtang->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Berhasil'], 200);
    }

    public function cetakFaktur($id)
    {
        $pembelian = Pembelian::findOrFail($id);
        return view('pembelian.faktur', compact('pembelian'));
    }


}
