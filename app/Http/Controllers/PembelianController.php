<?php

namespace App\Http\Controllers;

use App\Models\Item;
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
            'items.*.id' => 'required|exists:items,id',
            'items.*.jumlah' => 'required|integer|min:1',
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
        $items = Item::all();
        return view('pembelian.edit', compact('pembelian', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembelian $pembelian)
    {
        $rules = [
            'nama_supplier' => 'required|string',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:items,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ];

        $message = [
            'required' => ':attribute tidak boleh kosong',
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()->withErrors($validator)
                ->with('danger', 'Pastikan semua field terisi');
        } else {
            // Hapus detail penjualan yang ada
            foreach ($pembelian->pembelianDetails as $detail) {
                $item = Item::find($detail->item_id);
                $item->increment('stock', $detail->jumlah);
                $detail->delete();
            }

            // Tambahkan detail item baru
            $totalHarga = 0;
            $totalItem = 0;

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
                'total_item' => $totalItem,
                'nama_supplier' => $request->nama_supplier,
            ]);
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil diperbarui!');
        }
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
        $request->validate([
            'jumlah_uang' => 'nullable|required_if:metode_pembayaran,CASH|numeric|min:0',
            'metode_pembayaran' => 'required|in:CASH,KREDIT,CEK,TRANSFER',
            'kode_cek' => 'required_if:metode_pembayaran,CEK|string|nullable',
            'tanggal_cair' => 'required_if:metode_pembayaran,CEK|date|nullable',
        ]);

        $pembelian = Pembelian::findOrFail($id);
        $totalHarga = $pembelian->pembelianDetails->sum('total_harga');
        $jumlahUang = $request->jumlah_uang;
        $metode = $request->metode_pembayaran;

        if ($metode === 'CASH' && $jumlahUang < $totalHarga) {
            return response()->json(['success' => false, 'message' => 'Jumlah uang kurang!'], 400);
        }

        $kembalian = ($metode === 'CASH') ? $jumlahUang - $totalHarga : 0;

        $pembelian->status = 'LUNAS';
        $pembelian->kembalian = $kembalian;
        $pembelian->total_uang = $metode === 'CASH' ? $jumlahUang : 0;
        $pembelian->metode = $metode;

        if ($metode === 'CEK') {
            $pembelian->kode_cek = $request->kode_cek;
            $pembelian->tanggal_cair = $request->tanggal_cair;
        }

        $pembelian->save();

        return response()->json(['success' => true, 'message' => 'Pembelian berhasil dilunasi'], 200);
    }

    public function cetakFaktur($id)
    {
        $pembelian = Pembelian::findOrFail($id);
        return view('pembelian.faktur', compact('pembelian'));
    }


}
