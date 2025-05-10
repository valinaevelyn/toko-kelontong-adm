<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama', 'like', "%$search%")
                ->orWhere('merek', 'like', "%$search%")
                ->orWhere('kategori', 'like', "%$search%");
        }

        $items = $query->orderBy('nama')->paginate(10);

        return view('item.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('item.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'nama' => 'required',
            'merek' => 'required',
            'kategori' => 'required',
            'minimal_stock' => 'nullable|integer|min:0',
            'harga_jual' => 'required|numeric',
            'stock_dus' => 'nullable|integer',
            'stock_rcg' => 'nullable|integer',
            'stock_pcs' => 'nullable|integer',
            'dus_in_pcs' => 'nullable|integer',
            'rcg_in_pcs' => 'nullable|integer',


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
            Item::create([
                'nama' => $request->input('nama'),
                'merek' => $request->input('merek'),
                'minimal_stock' => $request->input('minimal_stock') ?? 0,
                'kategori' => $request->input('kategori'),
                'harga_jual' => $request->input('harga_jual'),
                'stock_dus' => $request->input('stock_dus') ?? 0,
                'stock_rcg' => $request->input('stock_rcg') ?? 0,
                'stock_pcs' => $request->input('stock_pcs') ?? 0,
                'dus_in_pcs' => $request->input('dus_in_pcs') ?? 0,
                'rcg_in_pcs' => $request->input('rcg_in_pcs') ?? 0,
            ]);

            return redirect()->route('item.index')->with('success', 'Data Item berhasil disimpan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        return view('item.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $rules = [
            'nama' => 'required',
            'merek' => 'required',
            'minimal_stock' => 'nullable|integer|min:0',
            'kategori' => 'required',
            'harga_jual' => 'required|numeric',
            'stock_dus' => 'nullable|integer|min:0',
            'stock_rcg' => 'nullable|integer|min:0',
            'stock_pcs' => 'nullable|integer|min:0',
            'dus_in_pcs' => 'nullable|integer|min:0',
            'rcg_in_pcs' => 'nullable|integer|min:0',
        ];

        $messages = [
            'required' => ':attribute wajib diisi.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus bilangan bulat.',
            'min' => ':attribute tidak boleh negatif.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator)
                ->with('danger', 'Pastikan semua field terisi dengan benar.');
        }

        $item->update([
            'nama' => $request->nama,
            'merek' => $request->merek,
            'minimal_stock' => $request->minimal_stock ?? 0,
            'kategori' => $request->kategori,
            'harga_jual' => $request->harga_jual,
            'stock_dus' => $request->stock_dus ?? 0,
            'stock_rcg' => $request->stock_rcg ?? 0,
            'stock_pcs' => $request->stock_pcs ?? 0,
            'dus_in_pcs' => $request->dus_in_pcs ?? 0,
            'rcg_in_pcs' => $request->rcg_in_pcs ?? 0,
        ]);

        return redirect()->route('item.index')->with('success', 'Item berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('item.index')->with('success', 'Data Item Berhasil Dihapus');
    }
}
