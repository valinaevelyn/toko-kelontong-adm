@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <br>
        <div class="text-start mb-3">
            {{-- <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#inputBiayaModal">
                Input Biaya
            </button> --}}
        </div>
        <br>
        <h2 class="text-center">LAPORAN UTANG</h2>

        <div class="mb-3 mt-3 text-center">
            <form action="{{ route('laporan.utang') }}" method="GET" class="d-inline-block">
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
                        <th>UTANG</th>
                        <th>TANGGAL JATUH TEMPO</th>
                        <th>STATUS TERLAMBAT (Hari)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporanUtang as $item)
                        <tr>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td>{{ 'Rp ' . number_format($item->jumlah_utang, 0, ',', '.') }}</td>
                            <td>{{ $item->jatuh_tempo }}</td>
                            <td>
                                @if($item->status_terlambat === 'Belum jatuh tempo')
                                    {{ $item->status_terlambat }}
                                @elseif($item->status_terlambat === 'Sudah lunas')
                                    {{ $item->status_terlambat }}
                                @else
                                    {{ $item->status_terlambat }} Hari Terlambat
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Input Utang -->
    <div class="modal fade" id="inputUtangModal" tabindex="-1" aria-labelledby="inputUtangModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inputUtangModalLabel">Input Utang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('laporan.utang.biaya') }}" method="POST">
                        @csrf
                        <!-- Form fields for utang -->
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection