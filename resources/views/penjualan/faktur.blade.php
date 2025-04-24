@extends('layouts.master')
@section('penjualanActive', 'active')
@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 text-center">
                <h2>Faktur Penjualan</h2>
                <p class="text-muted">Tanggal: {{ $penjualan->tanggal_penjualan }}</p>
                <p><strong>No. Faktur:</strong> {{ $penjualan->no_faktur }}</p>
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
                            <th scope="col">Nama Item</th>
                            <th scope="col">Jumlah Item</th>
                            <th scope="col">Harga Satuan</th>
                            <th scope="col">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalHarga = 0; @endphp
                        @foreach ($penjualan->penjualanDetails as $detail)
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
                            <td><strong>{{ 'Rp ' . number_format($penjualan->total_harga_akhir, 0, ',', '.') }}</strong>
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

    <style>
        .table {
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

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                margin-top: 0 !important;
                padding: 0 !important;
            }

            .d-flex.justify-content-end.mt-3 {
                display: none;
                /* Sembunyikan tombol cetak saat print */
            }

            @page {
                margin: 1cm;
            }

            .mt-4,
            .mt-3 {
                margin-top: 10px !important;
            }

            h2 {
                font-size: 20px;
                margin: 10px 0;
            }

            p {
                margin: 5px 0;
            }
        }
    </style>
@endsection