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
        <h2 class="text-center">LAPORAN ARUS KAS</h2>

        <!-- Filter berdasarkan bulan -->
        <div class="mb-3 mt-3 text-center">
            <form action="{{ route('laporan.kas') }}" method="GET" class="d-inline-block">
                <div class="row-md-10 mb-2">
                    <label for="bulan" class="form-label">PERIODE</label>
                    <select name="bulan" class="form-select d-inline-block w-auto ms-3" onchange="this.form.submit()">
                        <option value="ALL" {{ request('bulan') == 'ALL' ? 'selected' : '' }}>ALL</option>
                        @for ($i = 0; $i < 12; $i++)
                                            @php
                                                $date = now()->startOfYear()->addMonths($i);
                                                $value = $date->format('Y-m');
                                                $label = $date->translatedFormat('F Y'); // Format nama bulan dalam bahasa lokal
                                            @endphp
                                            <option value="{{ $value }}" {{ request('bulan') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                        @endfor
                    </select>

                </div>
            </form>
        </div>

        <hr>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>TANGGAL</th>
                        <th>KETERANGAN</th>
                        <th>DEBET</th>
                        <th>KREDIT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporanKas as $laporan)
                        <tr>
                            <td>{{ $laporan->tanggal }}</td>
                            <td>{{ $laporan->keterangan }}</td>
                            <td>{{ $laporan->kas_masuk ? 'Rp ' . number_format($laporan->kas_masuk, 0, ',', '.') : '-' }}</td>
                            <td>{{ $laporan->kas_keluar ? 'Rp ' . number_format($laporan->kas_keluar, 0, ',', '.') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
                        <form action="{{ route('laporan.kas.biaya') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" placeholder="Keterangan" required>
                            </div>
                            <div class="mb-3">
                                <label for="kas_masuk" class="form-label">Kas Masuk</label>
                                <input type="number" name="kas_masuk" class="form-control" placeholder="0">
                            </div>
                            <div class="mb-3">
                                <label for="kas_keluar" class="form-label">Kas Keluar</label>
                                <input type="number" name="kas_keluar" class="form-control" placeholder="0">
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