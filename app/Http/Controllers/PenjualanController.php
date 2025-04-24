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
        $items = Item::all();
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
            $hargaSatuan = $itemData->harga_jual;

            // if ($itemData->stock < $item['jumlah']) {
            //     return redirect()->route('penjualan.create')->with('error', 'Stok item tidak mencukupi!');
            // }

            // $pcsFromDus = $item['stock_dus'] * $itemData->dus_in_pcs;
            // $pcsFromRcg = $item['stock_rcg'] * $itemData->rcg_in_pcs;
            // $totalPcs = $pcsFromDus + $pcsFromRcg + $item['stock_pcs'];

            // Konversi ke PCS berdasarkan satuan yang dipilih
            $jumlahPCS = 0;

            $jumlah_dus = 0;
            $jumlah_rcg = 0;
            $jumlah_pcs = 0;

            switch ($item['satuan']) {
                case 'dus':
                    $jumlah_dus = $item['jumlah'];
                    $jumlahPCS = $item['jumlah'] * $itemData->dus_in_pcs;
                    break;
                case 'rcg':
                    $jumlah_rcg = $item['jumlah'];
                    $jumlahPCS = $item['jumlah'] * $itemData->rcg_in_pcs;
                    break;
                case 'pcs':
                    $jumlah_pcs = $item['jumlah'];
                    $jumlahPCS = $item['jumlah'];
                    break;
            }

            // Cek apakah stok mencukupi
            $stok_tersedia = ($itemData->stock_dus * $itemData->dus_in_pcs) +
                ($itemData->stock_rcg * $itemData->rcg_in_pcs) +
                $itemData->stock_pcs;

            if ($stok_tersedia < $jumlahPCS) {
                return redirect()->route('penjualan.create')->with('error', 'Stok item tidak mencukupi!');
            }


            PenjualanDetail::create([
                'penjualan_id' => $penjualan->id,
                'item_id' => $item['id'],
                'jumlah_dus' => $jumlah_dus,
                'jumlah_rcg' => $jumlah_rcg,
                'jumlah_pcs' => $jumlah_pcs,
                'jumlah' => $jumlahPCS,
                'harga_satuan' => $hargaSatuan,
            ]);

            $itemData->stock_dus -= $jumlah_dus;
            $itemData->stock_rcg -= $jumlah_rcg;
            $itemData->stock_pcs -= $jumlah_pcs;
            $itemData->save();

            $totalHarga += $jumlahPCS * $hargaSatuan;
            $totalItem += $jumlahPCS;
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
        $items = Item::all();
        return view('penjualan.edit', compact('penjualan', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Penjualan $penjualan)
    // {
    //     // Kembalikan stok item dari detail sebelumnya
    //     foreach ($penjualan->penjualanDetails as $detail) {
    //         $item = Item::find($detail->item_id);
    //         $item->increment('stock_dus', $detail->jumlah_dus);
    //         $item->increment('stock_rcg', $detail->jumlah_rcg);
    //         $item->increment('stock_pcs', $detail->jumlah_pcs);

    //         $detail->delete();
    //     }

    //     $totalHarga = 0;
    //     $totalItem = 0;

    //     foreach ($request->items as $item) {
    //         $itemData = Item::find($item['id']);

    //         $jumlahPCS = 0;
    //         $jumlah_dus = 0;
    //         $jumlah_rcg = 0;
    //         $jumlah_pcs = 0;

    //         switch ($item['satuan']) {
    //             case 'dus':
    //                 $jumlah_dus = $item['jumlah'];
    //                 $jumlahPCS = $item['jumlah'] * $itemData->dus_in_pcs;
    //                 break;
    //             case 'rcg':
    //                 $jumlah_rcg = $item['jumlah'];
    //                 $jumlahPCS = $item['jumlah'] * $itemData->rcg_in_pcs;
    //                 break;
    //             case 'pcs':
    //                 $jumlah_pcs = $item['jumlah'];
    //                 $jumlahPCS = $item['jumlah'];
    //                 break;
    //         }

    //         // Cek stok gabungan tersedia
    //         $stok_tersedia = ($itemData->stock_dus * $itemData->dus_in_pcs) +
    //             ($itemData->stock_rcg * $itemData->rcg_in_pcs) +
    //             $itemData->stock_pcs;

    //         if ($stok_tersedia < $jumlahPCS) {
    //             return redirect()->route('penjualan.edit', $penjualan)->with('error', 'Stok item tidak mencukupi!');
    //         }

    //         PenjualanDetail::create([
    //             'penjualan_id' => $penjualan->id,
    //             'item_id' => $item['id'],
    //             'jumlah_dus' => $jumlah_dus,
    //             'jumlah_rcg' => $jumlah_rcg,
    //             'jumlah_pcs' => $jumlah_pcs,
    //             'jumlah' => $jumlahPCS,
    //             'harga_satuan' => $item['harga_satuan'],
    //         ]);

    //         // Kurangi stok satuan yang sesuai
    //         $itemData->decrement('stock_dus', $jumlah_dus);
    //         $itemData->decrement('stock_rcg', $jumlah_rcg);
    //         $itemData->decrement('stock_pcs', $jumlah_pcs);





    //         $totalHarga += $jumlahPCS * $item['harga_satuan'];
    //         $totalItem += $jumlahPCS;
    //     }

    //     $penjualan->update([
    //         'nama_pembeli' => $request->nama_pembeli,
    //         'total_harga_akhir' => $totalHarga,
    //         'total_item' => $totalItem,
    //     ]);

    //     return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diperbarui!');
    // }

    public function update(Request $request, Penjualan $penjualan)
    {
        $totalHarga = 0;
        $totalItem = 0;

        $existingDetails = $penjualan->penjualanDetails->keyBy('item_id');

        foreach ($request->items as $itemInput) {
            $itemId = $itemInput['id'];
            $satuan = $itemInput['satuan'];
            $jumlahBaru = $itemInput['jumlah'];
            $hargaSatuan = $itemInput['harga_satuan'];

            $itemData = Item::find($itemId);
            $oldDetail = $existingDetails->get($itemId);

            // Konversi jumlah baru ke PCS
            switch ($satuan) {
                case 'dus':
                    $jumlah_dus_baru = $jumlahBaru;
                    $jumlah_rcg_baru = 0;
                    $jumlah_pcs_baru = 0;
                    $jumlah_pcs_total_baru = $jumlahBaru * $itemData->dus_in_pcs;
                    break;
                case 'rcg':
                    $jumlah_dus_baru = 0;
                    $jumlah_rcg_baru = $jumlahBaru;
                    $jumlah_pcs_baru = 0;
                    $jumlah_pcs_total_baru = $jumlahBaru * $itemData->rcg_in_pcs;
                    break;
                case 'pcs':
                default:
                    $jumlah_dus_baru = 0;
                    $jumlah_rcg_baru = 0;
                    $jumlah_pcs_baru = $jumlahBaru;
                    $jumlah_pcs_total_baru = $jumlahBaru;
                    break;
            }

            // Hitung jumlah PCS lama (jika ada detail sebelumnya)
            $jumlah_pcs_total_lama = 0;
            if ($oldDetail) {
                $jumlah_pcs_total_lama =
                    ($oldDetail->jumlah_dus * $itemData->dus_in_pcs) +
                    ($oldDetail->jumlah_rcg * $itemData->rcg_in_pcs) +
                    $oldDetail->jumlah_pcs;
            }

            $selisih = $jumlah_pcs_total_baru - $jumlah_pcs_total_lama;

            // Jika selisih > 0, berarti menambah jumlah penjualan → kurangi stok
            // Jika selisih < 0, berarti mengurangi jumlah penjualan → tambahkan stok
            if ($selisih > 0) {
                $stok_tersedia = ($itemData->stock_dus * $itemData->dus_in_pcs) +
                    ($itemData->stock_rcg * $itemData->rcg_in_pcs) +
                    $itemData->stock_pcs;

                if ($stok_tersedia < $selisih) {
                    return redirect()->route('penjualan.edit', $penjualan)->with('error', 'Stok item tidak mencukupi untuk item: ' . $itemData->nama);
                }

                // Kurangi stok satuan yang sesuai
                $itemData->decrement('stock_dus', $jumlah_dus_baru - ($oldDetail->jumlah_dus ?? 0));
                $itemData->decrement('stock_rcg', $jumlah_rcg_baru - ($oldDetail->jumlah_rcg ?? 0));
                $itemData->decrement('stock_pcs', $jumlah_pcs_baru - ($oldDetail->jumlah_pcs ?? 0));
            } else {
                // Tambahkan kembali selisih ke stok
                $itemData->increment('stock_dus', ($oldDetail->jumlah_dus ?? 0) - $jumlah_dus_baru);
                $itemData->increment('stock_rcg', ($oldDetail->jumlah_rcg ?? 0) - $jumlah_rcg_baru);
                $itemData->increment('stock_pcs', ($oldDetail->jumlah_pcs ?? 0) - $jumlah_pcs_baru);
            }

            // Update or create detail
            if ($oldDetail) {
                $oldDetail->update([
                    'jumlah_dus' => $jumlah_dus_baru,
                    'jumlah_rcg' => $jumlah_rcg_baru,
                    'jumlah_pcs' => $jumlah_pcs_baru,
                    'jumlah' => $jumlah_pcs_total_baru,
                    'harga_satuan' => $hargaSatuan,
                ]);
            } else {
                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'item_id' => $itemId,
                    'jumlah_dus' => $jumlah_dus_baru,
                    'jumlah_rcg' => $jumlah_rcg_baru,
                    'jumlah_pcs' => $jumlah_pcs_baru,
                    'jumlah' => $jumlah_pcs_total_baru,
                    'harga_satuan' => $hargaSatuan,
                ]);
            }

            $totalHarga += $jumlah_pcs_total_baru * $hargaSatuan;
            $totalItem += $jumlah_pcs_total_baru;
        }

        $penjualan->update([
            'nama_pembeli' => $request->nama_pembeli,
            'total_harga_akhir' => $totalHarga,
            'total_item' => $totalItem,
        ]);

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diperbarui!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penjualan $penjualan)
    {
        // Kembalikan stok item dari detail sebelumnya
        foreach ($penjualan->penjualanDetails as $detail) {
            $item = Item::find($detail->item_id);
            $item->increment('stock_dus', $detail->jumlah_dus);
            $item->increment('stock_rcg', $detail->jumlah_rcg);
            $item->increment('stock_pcs', $detail->jumlah_pcs);

            $detail->delete();
        }

        // Hapus penjualan
        $penjualan->delete();

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dihapus!');
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
        $totalHarga = $penjualan->total_harga_akhir;
        $jumlahUang = $request->jumlah_uang;
        $metode = $request->metode_pembayaran;

        if ($metode == 'CASH' && $jumlahUang < $totalHarga) {
            return response()->json(['success' => false, 'message' => 'Jumlah uang kurang!'], 400);
        }

        $kembalian = ($metode == 'CASH') ? $jumlahUang - $totalHarga : 0;

        // Set sesuai metode
        if ($metode == 'CASH') {
            $penjualan->total_uang = $jumlahUang;
            $penjualan->status = 'LUNAS';
        }

        $penjualan->kembalian = $kembalian;
        $penjualan->metode = $metode;

        if ($metode == 'TRANSFER') {
            $penjualan->status = 'LUNAS';
        }

        if ($metode == 'CEK') {
            $penjualan->kode_cek = $request->kode_cek;
            $penjualan->tanggal_cair = $request->tanggal_cair;
            $penjualan->status = $request->status_saldo;
        }

        if ($metode == 'KREDIT') {
            $penjualan->status = $request->status_saldo;
        }

        $penjualan->save();

        // Update laporan piutang jika tanggal cair tersedia
        if ($penjualan->tanggal_cair) {
            $laporanPiutang = LaporanPiutang::where('penjualan_id', $penjualan->id)->first();

            if ($laporanPiutang) {
                $laporanPiutang->status_terlambat = 'Sudah lunas';
                $laporanPiutang->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Berhasil'], 200);

    }

    public function cetakFaktur($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return view('penjualan.faktur', compact('penjualan'));
    }


}
