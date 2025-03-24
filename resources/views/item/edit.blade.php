@extends('layouts.master')

@section('content')
    <div class="container mt-4 mb-4">

        <div class="ml-1 fs-1 fw-3">
            Edit Item
        </div>


        <div class="d-flex justify-content-center">
            <div class="col-md-12 shadow mt-3 p-3 rounded">
                @include('partials.success')
                <form action="{{ route('item.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Item</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama"
                            value={{ old('nama', $item->nama) }}>

                        @error('nama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="merek" class="form-label">Merk Item</label>
                        <input type="text" class="form-control @error('merek') is-invalid @enderror" id="merek" name="merek"
                            value={{ old('merek', $item->merek) }}>

                        @error('merek')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="uom" class="form-label">UOM Item</label>
                        <input type="text" class="form-control @error('uom') is-invalid @enderror" id="uom" name="uom"
                            value={{ old('uom', $item->uom) }}>

                        @error('uom')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="harga_jual" class="form-label">Harga Jual</label>
                        <input type="number" class="form-control @error('harga_jual') is-invalid @enderror" id="harga_jual"
                            name="harga_jual" value={{ old('harga_jual', $item->harga_jual) }}>

                        @error('harga_jual')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="harga_beli" class="form-label">Harga Beli</label>
                        <input type="number" class="form-control @error('harga_beli') is-invalid @enderror" id="harga_beli"
                            name="harga_beli" value={{ old('harga_beli', $item->harga_beli) }}>

                        @error('harga_beli')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock Item</label>
                        <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock"
                            name="stock" value={{ old('stock', $item->stock) }}>

                        @error('stock')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('item.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection