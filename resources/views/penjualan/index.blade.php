@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 fs-1 fw-3">
                Penjualan
            </div>
        </div>

        <div class="col">
            @include('partials.danger')
            @include('partials.success')
            <div class="d-flex col mr-0  justify-content-end">

                {{-- <a href="{{ route('item.create') }}" class="btn btn-primary">Tambah Penjualan</a> --}}
            </div>
        </div>

        <div class="col mt-4">
            <table class="table table-bordered table-primary text-center">
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Nama Pembeli</th>
                        <th scope="col">Item</th>
                        <th scope="col">Total Harga</th>
                        <th scope="col">Total Item</th>
                        <th scope="col">Total Uang</th>
                        <th scope="col">Kembalian</th>
                        <th scope="col">Metode</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                        {{-- bisa lihat faktur dan pelunasan dan detail penjualan --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penjualans as $penjualan)
                        @if ($penjualan->count())
                            <tr class="table">
                                <td>{{ $penjualan->tanggal_penjualan }}</td>
                                <td>{{ $penjualan->nama_pembeli }}</td>
                                <td>
                                    {{-- loop item yang ada di penjualanDetails --}}
                                    @foreach ($penjualan->penjualanDetails as $penjualanDetail)
                                        {{ $penjualanDetail->item->nama }} ({{ $penjualanDetail->jumlah }})<br>
                                    @endforeach
                                </td>
                                <td>{{ 'Rp ' . number_format($penjualan->total_harga, 0, ',', '.') }}</td>
                                <td>{{ $penjualan->total_item }}</td>
                                <td>{{ 'Rp ' . number_format($penjualan->total_uang, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($penjualan->kembalian, 0, ',', '.') }}</td>
                                <td>{{ $penjualan->metode }}</td>
                                <td>{{ $penjualan->status }}</td>
                                <td>
                                    <div class="dropdown d-flex justify-content-center">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                            id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a class="dropdown-item" href="{{ route('penjualan.show', $penjualan->id) }}">Detail
                                                    Penjualan</a>
                                            </li>
                                            <li><a class="dropdown-item" href="">Pelunasan</a></li>
                                            <li><a class="dropdown-item" href="">Cetak Faktur</a></li>

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr class="table-secondary">
                                <td colspan="2">Tidak ada item tersedia.</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            {{ $penjualans->links() }}
        </div>

    </div>

@endsection