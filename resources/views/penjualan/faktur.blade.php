@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 text-center">
                <h2>Faktur Penjualan</h2>
                <p class="text-muted">Tanggal: {{ $penjualan->tanggal_penjualan }}</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Detail Pembelian</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama Pembeli:</strong> {{ $penjualan->nama_pembeli }}</p>
                        <p><strong>Metode Pembayaran:</strong> {{ $penjualan->metode }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span
                                class="badge bg-{{ $penjualan->status == 'LUNAS' ? 'success' : 'warning' }}">{{ $penjualan->status }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Detail Barang</h5>
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>Item</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penjualan->penjualanDetails as $detail)
                            <tr>
                                <td>{{ $detail->item->nama }}</td>
                                <td>{{ $detail->jumlah }}</td>
                                <td>{{ 'Rp ' . number_format($detail->item->harga_jual, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($detail->total_harga, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total Harga:</strong></td>
                            <td><strong>{{ 'Rp ' . number_format($penjualan->penjualanDetails->sum('total_harga'), 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total Uang:</strong></td>
                            <td><strong>{{ 'Rp ' . number_format($penjualan->total_uang, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Kembalian:</strong></td>
                            <td><strong>{{ 'Rp ' . number_format($penjualan->kembalian, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <a href="#" onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak Faktur</a>
        </div>
    </div>
@endsection