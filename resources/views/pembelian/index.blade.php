@extends('layouts.master')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 fs-1 fw-3">
                Pembelian
            </div>
        </div>

        <div class="col">
            @include('partials.danger')
            @include('partials.success')
            <div class="d-flex col mr-0 justify-content-end">
                <a href="{{ route('pembelian.create') }}" class="btn btn-primary">Tambah Pembelian</a>
            </div>
        </div>

        <div class="col mt-4">
            <table class="table table-bordered table-primary text-center">
                <thead>
                    <tr>
                        <th scope="col" class="align-middle">Tanggal</th>
                        <th scope="col" class="align-middle">Nama Supplier</th>
                        <th scope="col" class="align-middle">Item</th>
                        <th scope="col" class="align-middle">Total Harga</th>
                        <th scope="col" class="align-middle">Total Item</th>
                        <th scope="col" class="align-middle">Total Uang</th>
                        <th scope="col" class="align-middle">Kembalian</th>
                        <th scope="col" class="align-middle">Metode</th>
                        <th scope="col" class="align-middle">Status</th>
                        <th scope="col" class="align-middle">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembelians as $pembelian)
                        @if ($pembelian->count())
                            <tr class="table">
                                <td class="align-middle">{{ $pembelian->tanggal_pembelian }}</td>
                                <td class="align-middle">{{ $pembelian->nama_supplier }}</td>
                                <td class="align-middle">
                                    @foreach ($pembelian->pembelianDetails as $pembelianDetail)
                                        {{ $pembelianDetail->item->nama }} ({{ $pembelianDetail->jumlah }})<br>
                                    @endforeach
                                </td>
                                <td class="align-middle">
                                    {{ 'Rp ' . number_format($pembelian->pembelianDetails->sum('total_harga'), 0, ',', '.') }}
                                </td>
                                <td class="align-middle">{{ $pembelian->total_item }}</td>
                                <td class="align-middle">{{ 'Rp ' . number_format($pembelian->total_uang, 0, ',', '.') }}</td>
                                <td class="align-middle">{{ 'Rp ' . number_format($pembelian->kembalian, 0, ',', '.') }}</td>
                                <td class="align-middle">{{ $pembelian->metode }}</td>
                                <td class="align-middle">{{ $pembelian->status }}</td>
                                <td class="align-middle">
                                    <div class="dropdown d-flex justify-content-center">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                            id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a class="dropdown-item" href="{{ route('pembelian.show', $pembelian->id) }}">Detail
                                                    Pembelian</a></li>
                                            @if($pembelian->status == 'BELUM LUNAS')
                                                <li><a class="dropdown-item pelunasan-btn" href="#" data-id="{{ $pembelian->id }}"
                                                        data-total="{{ $pembelian->total_harga }}">
                                                        Pelunasan
                                                    </a></li>
                                            @endif
                                            @if($pembelian->status == 'LUNAS')
                                                <li><a class="dropdown-item" href="{{ route('pembelian.faktur', $pembelian->id) }}"
                                                        target="_blank">Cetak Faktur</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr class="table-secondary">
                                <td colspan="10">Tidak ada data pembelian tersedia.</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal Pelunasan -->
        <div class="modal fade" id="pelunasanModal" tabindex="-1" aria-labelledby="pelunasanModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pelunasan Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="pelunasanForm">
                            @csrf
                            <input type="hidden" id="pembelian_id">
                            <div class="mb-3">
                                <label class="form-label">Total Harga</label>
                                <input type="text" class="form-control" id="total_harga" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah Uang</label>
                                <input type="number" class="form-control" id="jumlah_uang" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <select class="form-control" id="metode_pembayaran" required>
                                    <option value="CASH">Cash</option>
                                    <option value="KREDIT">Kredit</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Konfirmasi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            {{ $pembelians->links() }}
        </div>

    </div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.pelunasan-btn').forEach(button => {
            button.addEventListener("click", function () {
                let pembelianId = this.dataset.id;
                let totalHarga = this.dataset.total;

                document.getElementById("pembelian_id").value = pembelianId;
                document.getElementById("total_harga").value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(totalHarga);

                let modal = new bootstrap.Modal(document.getElementById('pelunasanModal'));
                modal.show();
            });
        });

        document.getElementById("pelunasanForm").addEventListener("submit", function (e) {
            e.preventDefault();

            let pembelianId = document.getElementById("pembelian_id").value;
            let jumlahUang = parseFloat(document.getElementById("jumlah_uang").value);
            let metodePembayaran = document.getElementById("metode_pembayaran").value;

            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

            fetch(`/pembelian/pelunasan/${pembelianId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ jumlah_uang: jumlahUang, metode_pembayaran: metodePembayaran })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message); });
                    }
                    return response.json();
                })
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(error => alert("Error: " + error.message));
        });
    });
</script>