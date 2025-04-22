@extends('layouts.master')
@section('pembelianActive', 'active')
@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 text-center">
                <h2>Faktur Pembelian</h2>
                <p class="text-muted">Tanggal: {{ $pembelian->tanggal_pembelian }}</p>
                <p><strong>No. Faktur:</strong> {{ $pembelian->no_faktur }}</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Detail Pembelian</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama Supplier:</strong> {{ $pembelian->nama_supplier }}</p>
                        <p><strong>Metode Pembayaran:</strong> {{ $pembelian->metode }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong>
                            <span class="badge bg-{{ $pembelian->status == 'LUNAS' ? 'success' : 'warning' }}">
                                {{ $pembelian->status }}
                            </span>
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
                            <th scope="col">Nama Item</th>
                            <th scope="col">Jumlah Item</th>
                            <th scope="col">Harga Satuan</th>
                            <th scope="col">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalHarga = 0; @endphp
                        @foreach($pembelian->pembelianDetails as $detail)
                                            @php
                                                $subtotal = $detail->total_harga;
                                                $totalHarga += $subtotal;
                                            @endphp
                                            <tr class="table">
                                                <td>{{ $detail->item->nama }}</td>
                                                <td>
                                                    @if($detail->jumlah_dus) <strong>{{ $detail->jumlah_dus }} dus</strong><br> @endif
                                                    @if($detail->jumlah_rcg) <strong>{{ $detail->jumlah_rcg }} renceng</strong><br> @endif
                                                    @if($detail->jumlah_pcs) <strong>{{ $detail->jumlah_pcs }} pcs</strong> @endif
                                                </td>
                                                <td>{{ 'Rp ' . number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                                <td>{{ 'Rp ' . number_format(($detail->jumlah_dus * $detail->item->dus_in_pcs * $detail->harga_satuan) + ($detail->jumlah_rcg * $detail->item->rcg_in_pcs * $detail->harga_satuan) + (($detail->jumlah_pcs * $detail->harga_satuan)), 0, ',', '.') }}
                                                </td>
                                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total Harga:</strong></td>
                            <td><strong>{{ 'Rp ' . number_format($pembelian->total_harga, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total Uang:</strong></td>
                            <td><strong>{{ 'Rp ' . number_format($pembelian->total_uang, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Kembalian:</strong></td>
                            <td><strong>{{ 'Rp ' . number_format($pembelian->kembalian, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <a href="#" onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak Faktur</a>
        </div>
    </div>

    <style>
        <style>.table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background: #0056b3 !important;
            color: white;
        }

        .table tbody tr:hover {
            background: rgba(0, 86, 179, 0.1);
        }

        /* Styling untuk Card dan Tombol */
        .btn-primary {
            border-radius: 5px;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #0056b3 !important;
        }
    </style>
@endsection