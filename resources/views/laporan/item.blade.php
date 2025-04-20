@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <h2 class="text-center">LAPORAN HISTORI STOK ITEM</h2>

        <form action="{{ route('laporan.item') }}" method="GET" class="mb-4">
            <div class="row justify-content-center align-items-end">
                <div class="col-auto">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control"
                        value="{{ request('tanggal', now()->toDateString()) }}">
                </div>
                <div class="col-auto mt-4">
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>TANGGAL</th>
                        <th>NAMA ITEM</th>
                        {{-- <th>KETERANGAN</th> --}}
                        {{-- <th>UOM</th> --}}
                        <th>JUMLAH PEMBELIAN</th>
                        <th>JUMLAH PENJUALAN</th>
                        <th>SISA STOK (PCS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $d)
                        <tr>
                            <td>{{ $d['tanggal'] }}</td>
                            <td>{{ $d['nama'] }}</td>
                            {{-- <td>{{ $d['keterangan'] }}</td> --}}
                            {{-- <td>{{ $d['uom'] }}</td> --}}
                            <td>{{ $d['pembelian'] }}</td>
                            <td>{{ $d['penjualan'] }}</td>
                            <td>{{ $d['sisa_stok'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection