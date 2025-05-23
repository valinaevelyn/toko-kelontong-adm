@extends('layouts.master')
@section('pembelianActive', 'active')
@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 fs-1 fw-3">
                Detail Transaksi Pembelian #{{ $pembelian->id }}
            </div>
        </div>

        <div class="col">
            @include('partials.danger')
            @include('partials.success')
            <div class="d-flex col mr-0 justify-content-end">
                <a href="{{ route('pembelian.index') }}" class="btn btn-primary">Kembali</a>
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
                    @foreach ($pembelian->pembelianDetails as $detail)
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
                    <tr class="table">
                        <td colspan="3">Total Harga</td>
                        <td><strong>{{ 'Rp ' . number_format($pembelian->total_harga, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
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
    </style>
@endsection