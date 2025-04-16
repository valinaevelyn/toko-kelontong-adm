<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\LaporanPiutang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Str;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Penjualan::with('penjualanDetails.item')->latest();

        if ($request->has('status') && in_array($request->status, ['LUNAS', 'BELUM LUNAS'])) {
            $query->where('status', $request->status);
        }

        $penjualans = $query->paginate(10);

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

        $tanggal = Carbon::now();
        $prefix = 'F' . $tanggal->format('Ym'); // contoh: F202504
        $randomCode = strtoupper(Str::random(6)); // contoh: 6 karakter acak
        $noFaktur = $prefix . '-' . $randomCode;

        // Buat transaksi penjualan
        $penjualan = Penjualan::create([
            'no_faktur' => $noFaktur,
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

            $subtotal = $item['jumlah'] * $itemData->harga_jual;

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
        $items = Item::all()->where('stock', '>', 0);
        return view('penjualan.edit', compact('penjualan', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        $rules = [
            'nama_pembeli' => 'required|string',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:items,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ];

        $message = [
            'required' => ':attribute harus diisi',
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()->withErrors($validator)
                ->with('danger', 'Pastikan semua field terisi');
        } else {
            // Hapus detail penjualan yang ada
            foreach ($penjualan->penjualanDetails as $detail) {
                $item = Item::find($detail->item_id);
                $item->increment('stock', $detail->jumlah);
                $detail->delete();
            }

            // Tambahkan detail item baru
            $totalHarga = 0;
            $totalItem = 0;

            foreach ($request->items as $item) {
                $itemData = Item::find($item['id']);

                if ($itemData->stock < $item['jumlah']) {
                    return redirect()->route('penjualan.edit', $penjualan)->with('error', 'Stok item tidak mencukupi!');
                }

                $subtotal = $item['jumlah'] * $itemData->harga_jual;

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
                'nama_pembeli' => $request->nama_pembeli,
                'total_harga_akhir' => $totalHarga,
                'total_item' => $totalItem,
            ]);

            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diperbarui!');
        }
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
        $request->validate([
            'jumlah_uang' => 'nullable|required_if:metode_pembayaran,CASH|numeric|min:0',
            'metode_pembayaran' => 'required|in:CASH,KREDIT,CEK,TRANSFER',
            'kode_cek' => 'required_if:metode_pembayaran,CEK|string|nullable',
            'tanggal_cair' => 'required_if:metode_pembayaran,CEK|date|nullable',
        ]);

        $penjualan = Penjualan::findOrFail($id);
        $totalHarga = $penjualan->penjualanDetails->sum('total_harga');
        $jumlahUang = $request->jumlah_uang;
        $metode = $request->metode_pembayaran;

        if ($metode == 'CASH' && $jumlahUang < $totalHarga) {
            return response()->json(['success' => false, 'message' => 'Jumlah uang kurang!'], 400);
        }

        $kembalian = ($metode == 'CASH') ? $jumlahUang - $totalHarga : 0;

        $penjualan->status = 'LUNAS';
        $penjualan->kembalian = $kembalian;
        $penjualan->total_uang = $metode == 'CASH' ? $jumlahUang : 0;
        $penjualan->metode = $metode;

        if ($metode == 'CEK') {
            $penjualan->kode_cek = $request->kode_cek;
            $penjualan->tanggal_cair = $request->tanggal_cair;
        }

        // Simpan perubahan di penjualan
        $penjualan->save();

        // Update status_terlambat di laporan piutang berdasarkan id penjualan
        if ($penjualan->tanggal_cair) {
            // Cari laporan piutang yang terkait dengan penjualan berdasarkan id
            $laporanPiutang = LaporanPiutang::where('penjualan_id', $penjualan->id)->first();

            if ($laporanPiutang) {
                // Jika ada, update status_terlambat menjadi "Sudah lunas"
                $laporanPiutang->status_terlambat = 'Sudah lunas';
                $laporanPiutang->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Penjualan berhasil dilunasi'], 200);
    }

    public function cetakFaktur($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('penjualan.faktur', compact('penjualan'));
    }


}
