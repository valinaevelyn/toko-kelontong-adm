@extends('layouts.master')
@section('content')
    <h2>Laporan Kas dan Bank</h2>
    <table border="1" class="table table-bordered text-center">
        <tr>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Kas Masuk</th>
            <th>Kas Keluar</th>
        </tr>
        @foreach($laporanKas as $laporan)
            <tr>
                <td>{{ $laporan->tanggal }}</td>
                <td>{{ $laporan->keterangan }}</td>
                <td>{{ $laporan->kas_masuk }}</td>
                <td>{{ $laporan->kas_keluar }}</td>
            </tr>
        @endforeach
    </table>

    <h3>Input Biaya</h3>
    <form action="{{ route('laporan.kas.biaya') }}" method="POST">
        @csrf
        <input type="date" name="tanggal" required>
        <input type="text" name="keterangan" placeholder="Keterangan" required>
        <input type="number" name="kas_masuk" placeholder="Kas Masuk">
        <input type="number" name="kas_keluar" placeholder="Kas Keluar">
        <button type="submit">Simpan</button>
    </form>
@endsection