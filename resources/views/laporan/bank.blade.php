@extends('layouts.master')
@section('content')
    <div class="container mt-4">
        <!-- Button trigger modal -->
        <br>
        <div class="text-start mb-3">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#inputBiayaModal">
                Input Biaya
            </button>
        </div>
        <br>
        <h2 class="text-center">LAPORAN ARUS BANK</h2>

        <!-- Filter berdasarkan bulan -->
        <div class="mb-3 mt-3 text-center">
            <form action="{{ route('laporan.bank') }}" method="GET" class="d-inline-block">
                <div class="row justify-content-center align-items-end">
                    <div class="col-auto">
                        <label for="bulan" class="form-label">PERIODE BULAN</label>
                        <select name="bulan" class="form-select" required>
                            <option value="ALL" {{ request('bulan') == 'ALL' ? 'selected' : '' }}>ALL</option>
                            @for ($i = 0; $i < 12; $i++)
                                                    @php
                                                        $date = now()->startOfYear()->addMonths($i);
                                                        $value = $date->format('Y-m');
                                                        $label = $date->translatedFormat('F Y');
                                                    @endphp
                                                    <option value="{{ $value }}" {{ request('bulan') == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-auto">
                        <label for="tanggal" class="form-label">FILTER TANGGAL (Opsional)</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
                    </div>

                    <div class="col-auto mt-4">
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <hr>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>TANGGAL</th>
                        <th>NAMA</th>
                        <th>KETERANGAN</th>
                        <th>DEBET</th>
                        <th>KREDIT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporanBank as $laporan)
                        <tr>
                            <td>{{ $laporan->tanggal }}</td>
                            <td>{{ $laporan->nama }}</td>
                            <td>{{ $laporan->keterangan }}</td>
                            <td>{{ $laporan->bank_masuk ? 'Rp ' . number_format($laporan->bank_masuk, 0, ',', '.') : '-' }}</td>
                            <td>{{ $laporan->bank_keluar ? 'Rp ' . number_format($laporan->bank_keluar, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h5 class="text-center">Saldo Akhir</h5>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Saldo Akhir</th>
                            <td class="text-end fw-bold">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="inputBiayaModal" tabindex="-1" aria-labelledby="inputBiayaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inputBiayaModalLabel">Input Biaya</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('laporan.bank.biaya') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" name="nama" class="form-control" placeholder="Nama" required>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" placeholder="Keterangan" required>
                            </div>
                            <div class="mb-3">
                                <label for="bank_masuk" class="form-label">Bank Masuk</label>
                                <input type="number" name="bank_masuk" class="form-control" placeholder="0">
                            </div>
                            <div class="mb-3">
                                <label for="bank_keluar" class="form-label">Bank Keluar</label>
                                <input type="number" name="bank_keluar" class="form-control" placeholder="0">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-success">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection