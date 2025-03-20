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
    public function index()
    {
        // $items = Item::all();
        $items = Item::paginate(10);
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
            'uom' => 'required',
            'harga' => 'required',
            'stock' => 'required'
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
                'uom' => $request->input('uom'),
                'harga' => $request->input('harga'),
                'stock' => $request->input('stock')
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
            'uom' => 'required',
            'harga' => 'required',
            'stock' => 'required'
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
            $item->nama = $request->nama;
            $item->merek = $request->merek;
            $item->uom = $request->uom;
            $item->harga = $request->harga;
            $item->stock = $request->stock;
            $item->save();

            return redirect()->route('item.index')->with('success', 'Data Item berhasil diperbarui');
        }
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
