<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembelians = Pembelian::latest()->paginate(10);
        return view('pembelian.index', compact('pembelians'));
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
            'items.*.id' => 'required|exists:items,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        // Buat transaksi pembelian
        $pembelian = Pembelian::create([
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

            $subtotal = $item['jumlah'] * $itemData->harga_beli;

            PembelianDetail::create([
                'pembelian_id' => $pembelian->id,
                'item_id' => $item['id'],
                'jumlah' => $item['jumlah'],
                'total_harga' => $subtotal,
            ]);

            // Tambah stok item
            $itemData->increment('stock', $item['jumlah']);
            $totalHarga += $subtotal;
            $totalItem += $item['jumlah'];
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembelian $pembelian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembelian $pembelian)
    {
        //
    }

    public function pelunasan(Request $request, $id)
    {
        $pembelian = Pembelian::findOrFail($id);
        $jumlahUang = $request->jumlah_uang;
        $metode = $request->metode_pembayaran;
        $totalHarga = $pembelian->pembelianDetails->sum('total_harga');

        if ($jumlahUang < $totalHarga) {
            return response()->json(['success' => false, 'message' => 'Jumlah uang kurang!']);
        }

        $kembalian = $jumlahUang - $totalHarga;

        // Update status pembelian
        $pembelian->status = 'LUNAS';
        $pembelian->kembalian = $kembalian;
        $pembelian->total_uang = $jumlahUang;
        $pembelian->metode = $metode;
        $pembelian->save(); // Simpan perubahan

        return response()->json(['success' => true, 'message' => 'Pembelian berhasil dilunasi']);
    }

    public function cetakFaktur($id)
    {
        $pembelian = Pembelian::findOrFail($id);
        return view('pembelian.faktur', compact('pembelian'));
    }


}
