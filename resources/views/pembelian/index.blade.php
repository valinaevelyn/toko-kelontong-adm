@extends('layouts.master')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('pembelianActive', 'active')
@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 fs-1 fw-3">
                Pembelian
            </div>
        </div>

        <hr>

        <div class="col">
            @include('partials.danger')
            @include('partials.success')
            <div class="d-flex col mr-0 justify-content-end">
                <a href="{{ route('pembelian.create') }}" class="btn btn-success">Tambah Pembelian</a>
            </div>
        </div>

        <div class="col mt-4 mb-5 table-container">
            <div class="d-flex justify-content-start mb-3">
                <form action="{{ route('pembelian.index') }}" method="GET" class="d-flex">
                    <select name="status" class="form-select me-2" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="LUNAS" {{ request('status') == 'LUNAS' ? 'selected' : '' }}>LUNAS</option>
                        <option value="BELUM LUNAS" {{ request('status') == 'BELUM LUNAS' ? 'selected' : '' }}>BELUM LUNAS
                        </option>
                    </select>
                    <noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>
                </form>
            </div>

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
                                <label class="form-label">Metode Pembayaran</label>
                                <select class="form-control" id="metode_pembayaran" required>
                                    <option value="CASH">Cash</option>
                                    <option value="KREDIT">Kredit</option>
                                    <option value="CEK">Cek</option>
                                    <option value="TRANSFER">Transfer</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Harga</label>
                                <input type="text" class="form-control" id="total_harga" readonly>
                            </div>
                            <div class="mb-3" id="jumlah_uang_group">
                                <label class="form-label">Jumlah Uang</label>
                                <input type="number" class="form-control" id="jumlah_uang">
                            </div>

                            <div class="mb-3" id="cek-fields-code">
                                <label class="form-label">Kode Cek</label>
                                <input type="text" class="form-control" id="kode_cek">
                            </div>
                            <div class="mb-3" id="cek-fields-date">
                                <label class="form-label">Tanggal Cair</label>
                                <input type="date" class="form-control" id="tanggal_cair">
                            </div>

                            <button type="submit" class="btn btn-primary">Konfirmasi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="d-flex justify-content-end">
            {{ $pembelians->appends(['status' => request('status')])->links() }}
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

        /* Styling Modal */
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background-color: #007bff !important;
            color: white;
        }

        .table {
            overflow: visible !important;
        }
    </style>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Menangani klik tombol pelunasan untuk pembelian
        document.querySelectorAll('.pelunasan-btn').forEach(button => {
            button.addEventListener("click", function () {
                let pembelianId = this.dataset.id;
                let totalHarga = this.dataset.total;

                // Mengisi data ke dalam modal
                document.getElementById("pembelian_id").value = pembelianId;
                document.getElementById("total_harga").value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(totalHarga);

                // Tampilkan modal
                let modal = new bootstrap.Modal(document.getElementById('pelunasanModal'));
                modal.show();
            });
        });

        // Fungsi untuk menyembunyikan/menampilkan input berdasarkan metode pembayaran
        function applyPaymentMethodRule() {
            let metode = document.getElementById("metode_pembayaran").value;

            if (metode === 'CASH') {
                document.getElementById("jumlah_uang_group").style.display = 'block';
                document.getElementById("cek-fields-code").style.display = 'none';
                document.getElementById("cek-fields-date").style.display = 'none';
            } else if (metode === 'CEK') {
                document.getElementById("jumlah_uang_group").style.display = 'none';
                document.getElementById("cek-fields-code").style.display = 'block';
                document.getElementById("cek-fields-date").style.display = 'block';
            } else {
                document.getElementById("jumlah_uang_group").style.display = 'none';
                document.getElementById("cek-fields-code").style.display = 'none';
                document.getElementById("cek-fields-date").style.display = 'none';
            }
        }

        // Jalankan saat dropdown metode pembayaran berubah
        document.getElementById("metode_pembayaran").addEventListener("change", applyPaymentMethodRule);

        // Jalankan saat halaman pertama kali dimuat
        applyPaymentMethodRule();

        // Tangani submit form pelunasan pembelian
        document.getElementById("pelunasanForm").addEventListener("submit", function (e) {
            e.preventDefault();

            let pembelianId = document.getElementById("pembelian_id").value;
            let jumlahUang = parseFloat(document.getElementById("jumlah_uang").value) || 0;
            let metodePembayaran = document.getElementById("metode_pembayaran").value;
            let kodeCek = document.getElementById("kode_cek").value;
            let tanggalCair = document.getElementById("tanggal_cair").value;

            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

            let requestData = {
                jumlah_uang: jumlahUang,
                metode_pembayaran: metodePembayaran,
                kode_cek: kodeCek,
                tanggal_cair: tanggalCair
            };

            fetch(`/pembelian/pelunasan/${pembelianId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify(requestData)
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