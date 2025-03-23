<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penjualans = Penjualan::latest()->paginate(10);
        return view('penjualan.index', compact('penjualans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::all()->where('stock', '>', 0);
        return view('penjualan.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pembeli' => 'required|string',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:items,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        // Buat transaksi penjualan
        $penjualan = Penjualan::create([
            'nama_pembeli' => $request->nama_pembeli,
            'tanggal_penjualan' => now(),
            'total_harga_akhir' => 0, // Akan di-update setelah menambahkan detail
            'total_item' => 0, // Akan di-update setelah menambahkan detail
            'total_uang' => 0,
            'kembalian' => 0,
            'metode' => '',
            'status' => 'BELUM LUNAS',
        ]);

        $totalHarga = 0;
        $totalItem = 0;

        // Tambahkan detail item
        foreach ($request->items as $item) {
            $itemData = Item::find($item['id']);

            if ($itemData->stock < $item['jumlah']) {
                return redirect()->route('penjualan.create')->with('error', 'Stok item tidak mencukupi!');
            }

            $subtotal = $item['jumlah'] * $itemData->harga;

            PenjualanDetail::create([
                'penjualan_id' => $penjualan->id,
                'item_id' => $item['id'],
                'jumlah' => $item['jumlah'],
                'total_harga' => $subtotal,
            ]);

            // Kurangi stok item
            $itemData->decrement('stock', $item['jumlah']);
            $totalHarga += $subtotal;
            $totalItem += $item['jumlah'];
        }

        // Update total harga penjualan
        $penjualan->update([
            'total_harga_akhir' => $totalHarga,
            'total_item' => $totalItem,
        ]);

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Penjualan $penjualan)
    {
        return view('penjualan.show', compact('penjualan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penjualan $penjualan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penjualan $penjualan)
    {
        //
    }

    public function pelunasan(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $jumlahUang = $request->jumlah_uang;
        $metode = $request->metode_pembayaran;
        $totalHarga = $penjualan->penjualanDetails->sum('total_harga');

        if ($jumlahUang < $totalHarga) {
            return response()->json(['success' => false, 'message' => 'Jumlah uang kurang!']);
        }

        $kembalian = $jumlahUang - $totalHarga;

        // Gunakan update atau save
        $penjualan->status = 'LUNAS';
        $penjualan->kembalian = $kembalian;
        $penjualan->total_uang = $jumlahUang;
        $penjualan->metode = $metode;
        $penjualan->save(); // Simpan perubahan

        // $penjualan->update([
        //     'status' => 'LUNAS',
        //     'kembalian' => $kembalian,
        //     'total_uang' => $jumlahUang,
        //     'metode' => $metode,
        // ]);

        return response()->json(['success' => true, 'message' => 'Penjualan berhasil dilunasi']);
    }

    public function cetakFaktur($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('penjualan.faktur', compact('penjualan'));
    }


}
