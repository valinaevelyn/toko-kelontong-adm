@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 fs-1 fw-3">
                Detail Transaksi Penjualan #{{ $penjualan->id }}
            </div>
        </div>

        <div class="col">
            @include('partials.danger')
            @include('partials.success')
            <div class="d-flex col mr-0  justify-content-end">
                <a href="{{ route('penjualan.index') }}" class="btn btn-primary">Kembali</a>
            </div>
        </div>

        <div class="col mt-4">
            <table class="table table-bordered table-primary text-center">
                <thead>
                    <tr>
                        <th scope="col">Nama Item</th>
                        <th scope="col">Jumlah Item</th>
                        <th scope="col">Harga Satuan</th>
                        <th scope="col">Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalHarga = 0; @endphp
                    {{-- @foreach ($penjualan as $p) --}}
                    <tr class="table">
                        @foreach ($penjualan->penjualanDetails as $detail)
                                @php $subtotal = $detail->jumlah * $detail->item->harga; @endphp
                                @php $totalHarga += $subtotal; @endphp
                            <tr class="table">
                                <td>{{ $detail->item->nama }}</td>
                                <td>{{ $detail->jumlah }}</td>
                                <td>{{ 'Rp ' . number_format($detail->item->harga, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    {{-- @endforeach --}}
                    <tr class="table">
                        <td colspan="3">Total Harga</td>
                        <td><strong>{{ 'Rp ' . number_format($totalHarga, 0, ',', '.') }}</strong></td>


                    </tr>
                </tbody>
            </table>
        </div>

    </div>

@endsection