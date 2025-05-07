@extends('layouts.master')
@section('itemActive', 'active')
@section('content')
    <div class="container mt-4 mb-4">
        <div class="ml-1 fs-1 fw-3">Edit Item</div>

        <div class="d-flex justify-content-center">
            <div class="col-md-12 shadow mt-3 p-3 rounded">
                @include('partials.success')
                @include('partials.danger')
                <form action="{{ route('item.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Item</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama"
                            value="{{ old('nama', $item->nama) }}">
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="merek" class="form-label">Merk Item</label>
                        <input type="text" class="form-control @error('merek') is-invalid @enderror" id="merek" name="merek"
                            value="{{ old('merek', $item->merek) }}">
                        @error('merek')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori Item</label>
                        <input type="text" class="form-control @error('kategori') is-invalid @enderror" id="kategori"
                            name="kategori" value="{{ old('kategori', $item->kategori) }}">
                        @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="harga_jual" class="form-label">Harga Jual</label>
                        <input type="number" class="form-control @error('harga_jual') is-invalid @enderror" id="harga_jual"
                            name="harga_jual" value="{{ old('harga_jual', $item->harga_jual) }}">
                        @error('harga_jual')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="stock_dus" class="form-label">Stock Dus</label>
                            <input type="number" class="form-control @error('stock_dus') is-invalid @enderror"
                                name="stock_dus" value="{{ old('stock_dus', $item->stock_dus) }}">
                            @error('stock_dus')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="stock_rcg" class="form-label">Stock Renceng</label>
                            <input type="number" class="form-control @error('stock_rcg') is-invalid @enderror"
                                name="stock_rcg" value="{{ old('stock_rcg', $item->stock_rcg) }}">
                            @error('stock_rcg')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="stock_pcs" class="form-label">Stock PCS</label>
                            <input type="number" class="form-control @error('stock_pcs') is-invalid @enderror"
                                name="stock_pcs" value="{{ old('stock_pcs', $item->stock_pcs) }}">
                            @error('stock_pcs')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dus_in_pcs" class="form-label">Konversi 1 Dus = ... PCS</label>
                            <input type="number" class="form-control @error('dus_in_pcs') is-invalid @enderror"
                                name="dus_in_pcs" value="{{ old('dus_in_pcs', $item->dus_in_pcs) }}">
                            @error('dus_in_pcs')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="rcg_in_pcs" class="form-label">Konversi 1 Renceng = ... PCS</label>
                            <input type="number" class="form-control @error('rcg_in_pcs') is-invalid @enderror"
                                name="rcg_in_pcs" value="{{ old('rcg_in_pcs', $item->rcg_in_pcs) }}">
                            @error('rcg_in_pcs')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('item.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection