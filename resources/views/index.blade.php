@extends('layouts.master')

@section('dashboardActive', 'active')
@section('content')

    <style>
        /* Styling untuk Card */
        .card {
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out, box-shadow 0.3s;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        /* Styling untuk Tabel */
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

        .table td {
            vertical-align: middle;
        }

        /* Warna Text untuk Status Transaksi */
        .text-success {
            font-weight: bold;
            color: #28a745 !important;
        }

        .text-danger {
            font-weight: bold;
            color: #dc3545 !important;
        }
    </style>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    </div>

    <div class="row d-flex justify-content-center">
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pendapatan</h5>
                    <h3>Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pengeluaran</h5>
                    <h3>Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Keuntungan Bersih</h5>
                    <h3>Rp {{ number_format($keuntungan_bersih, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex justify-content-center">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Saldo Kas</h5>
                    <h3>Rp {{ number_format($saldo_kas, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Saldo Bank</h5>
                    <h3>Rp {{ number_format($saldo_bank, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Saldo Piutang</h5>
                    <h3>Rp {{ number_format($saldo_piutang, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Button untuk buka modal -->
    <div class="text-center mt-4">
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#transferModal">
            Pindahkan Saldo Bank/Kas
        </button>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('dashboard.transferSaldo') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="transferModalLabel">Transfer Saldo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="dari" class="form-label">Dari</label>
                            <select class="form-select" name="dari" id="dari" required>
                                <option value="BANK">Bank</option>
                                <option value="KAS">Kas</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ke" class="form-label">Ke</label>
                            <select class="form-select" name="ke" id="ke" required>
                                <option value="KAS">Kas</option>
                                <option value="BANK">Bank</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah Transfer</label>
                            <input type="number" name="jumlah" class="form-control" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan (Opsional)</label>
                            <input type="text" name="catatan" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Transfer</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <br>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3">Transaksi Terbaru</h1>
    </div>
    <table class="table">
        <thead class="table-dark">
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Jenis Transaksi</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi_terbaru as $trx)
                <tr>
                    <td class="align-middle">{{ $trx->tanggal }}</td>
                    <td class="align-middle">{{ $trx->kategori }}</td>
                    <td class="align-middle">{{ $trx->jenis }}</td>
                    <td class="{{ $trx->jenis == 'Masuk' ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


@endsection