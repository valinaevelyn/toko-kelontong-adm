@extends('layouts.master')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('penjualanActive', 'active')
@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 fs-1 fw-3">
                Penjualan
            </div>
        </div>
        <hr>

        <div class="col">
            @include('partials.danger')
            @include('partials.success')
            <div class="d-flex col mr-0  justify-content-end">

                <a href="{{ route('penjualan.create') }}" class="btn btn-success">Tambah Penjualan</a>
            </div>
        </div>

        <div class="col mt-4">
            <div class="d-flex justify-content-start mb-3">
                <form action="{{ route('penjualan.index') }}" method="GET" class="d-flex">
                    <!-- Filter Status -->
                    <select name="status" class="form-select me-2" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="LUNAS" {{ request('status') == 'LUNAS' ? 'selected' : '' }}>LUNAS</option>
                        <option value="BELUM LUNAS" {{ request('status') == 'BELUM LUNAS' ? 'selected' : '' }}>BELUM LUNAS
                        </option>
                    </select>

                    <!-- Search Field -->
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari penjualan..."
                        value="{{ request('search') }}">

                    <!-- Submit Button (For Non-JavaScript Browsers) -->
                    <button type="submit" class="btn btn-primary">Cari</button>
                </form>
            </div>

            <table class="table table-bordered table-primary">
                <thead>
                    <tr>
                        <th scope="col" class="align-middle text-center">Tanggal</th>
                        <th scope="col" class="align-middle text-center">Nama Pembeli</th>
                        <th scope="col" class="align-middle text-center">Item</th>
                        <th scope="col" class="align-middle text-center">Total Harga</th>
                        <th scope="col" class="align-middle text-center">Total Item</th>
                        <th scope="col" class="align-middle text-center">Total Uang</th>
                        <th scope="col" class="align-middle text-center">Kembalian</th>
                        <th scope="col" class="align-middle text-center">Metode</th>
                        <th scope="col" class="align-middle text-center">Status</th>
                        <th scope="col" class="align-middle text-center">Action</th>
                        {{-- bisa lihat faktur dan pelunasan dan detail penjualan --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penjualans as $penjualan)
                        @if ($penjualan->count())
                            <tr class="table">
                                <td class="align-middle">{{ $penjualan->tanggal_penjualan }}</td>
                                <td class="align-middle">{{ $penjualan->nama_pembeli }}</td>
                                <td class="align-middle">
                                    {{-- loop item yang ada di penjualanDetails --}}
                                    @foreach ($penjualan->penjualanDetails as $penjualanDetail)
                                        <b>{{ $penjualanDetail->item->nama }}</b> :
                                        @if($penjualanDetail->jumlah_dus) {{ $penjualanDetail->jumlah_dus }} dus @endif
                                        @if($penjualanDetail->jumlah_rcg) {{ $penjualanDetail->jumlah_rcg }} rcg @endif
                                        @if($penjualanDetail->jumlah_pcs) {{ $penjualanDetail->jumlah_pcs }} pcs @endif<br>
                                    @endforeach
                                </td>
                                <td class="align-middle">
                                    {{ 'Rp ' . number_format($penjualan->total_harga_akhir, 0, ',', '.') }}
                                </td>

                                <td class="align-middle">{{ $penjualan->total_item }}</td>
                                <td class="align-middle">{{ 'Rp ' . number_format($penjualan->total_uang, 0, ',', '.') }}</td>
                                <td class="align-middle">{{ 'Rp ' . number_format($penjualan->kembalian, 0, ',', '.') }}</td>
                                <td class="align-middle">{{ $penjualan->metode }}</td>
                                <td class="align-middle">{{ $penjualan->status }}</td>
                                <td class="align-middle">
                                    <div class="dropdown d-flex justify-content-center">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                            id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a class="dropdown-item" href="{{ route('penjualan.show', $penjualan->id) }}">Detail
                                                    Penjualan</a>
                                            </li>
                                            @if($penjualan->status == 'BELUM LUNAS')
                                                <li><a class="dropdown-item pelunasan-btn" href="#" data-id="{{ $penjualan->id }}"
                                                        data-total="{{ $penjualan->total_harga_akhir }}">
                                                        Pelunasan
                                                    </a></li>
                                            @endif

                                            @if($penjualan->status == 'LUNAS' || ($penjualan->status == 'BELUM LUNAS' && $penjualan->metode == 'KREDIT') || $penjualan->metode == 'CEK')
                                                <li><a class="dropdown-item" href="{{ route('penjualan.faktur', $penjualan->id) }}"
                                                        target="_blank">Cetak Faktur</a></li>
                                            @endif

                                            <li><a class="dropdown-item" href="{{ route('penjualan.edit', $penjualan->id) }}">Edit
                                                    Penjualan</a></li>

                                            @CAN('supervisor')
                                                <li>
                                                    <form onclick="confirm('are you sure?')"
                                                        action="{{ route('penjualan.destroy', $penjualan->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item">Delete</a>
                                                    </form>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr class="table-secondary">
                                <td colspan="10">Tidak ada item tersedia.</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal Pelunasan -->
        @if($penjualans->isNotEmpty())
            <div class="modal fade" id="pelunasanModal" tabindex="-1" aria-labelledby="pelunasanModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Pelunasan Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="pelunasanForm" method="POST" action="{{ route('penjualan.pelunasan', $penjualan->id) }}">
                                {{-- CSRF Token untuk keamanan --}}
                                @csrf
                                @method('PUT')
                                {{-- Input hidden untuk menyimpan ID penjualan --}}
                                <input type="hidden" id="penjualan_id">
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
                                    <label class="form-label">Tanggal Cair Cek</label>
                                    <input type="date" class="form-control" id="tanggal_cair">
                                </div>

                                <div class="mb-3" id="status_saldo_group" style="display: none;">
                                    <label class="form-label">Status Saldo</label>
                                    <select class="form-control" id="status_saldo" name="status_saldo">
                                        <option value="LUNAS">Lunas</option>
                                        <option value="BELUM LUNAS">Belum Lunas</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Konfirmasi</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif



        <div class="d-flex justify-content-end">
            {{ $penjualans->appends(['status' => request('status')])->links() }}
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
        // Menangani klik tombol pelunasan untuk memunculkan modal
        document.querySelectorAll('.pelunasan-btn').forEach(button => {
            button.addEventListener("click", function () {
                let penjualanId = this.dataset.id;
                let totalHarga = this.dataset.total;

                // Mengisi data ke dalam modal
                document.getElementById("penjualan_id").value = penjualanId;
                document.getElementById("total_harga").value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(totalHarga);

                let modal = new bootstrap.Modal(document.getElementById('pelunasanModal'));
                modal.show();
            });
        });

        // Menangani perubahan pada dropdown metode pembayaran
        document.getElementById("metode_pembayaran").addEventListener("change", function () {
            let metode = this.value;

            // Menyembunyikan atau menampilkan field berdasarkan metode pembayaran
            if (metode === 'CASH') {
                document.getElementById("jumlah_uang_group").style.display = 'block'; // tampilkan input jumlah uang
                document.getElementById("cek-fields-code").style.display = 'none';
                document.getElementById("cek-fields-date").style.display = 'none';
                document.getElementById("status_saldo_group").style.display = 'none';
            } else if (metode === 'CEK') {
                document.getElementById("jumlah_uang_group").style.display = 'none'; // sembunyikan input jumlah uang
                document.getElementById("cek-fields-code").style.display = 'block'; // tampilkan input cek
                document.getElementById("cek-fields-date").style.display = 'block'; // tampilkan input cek
                document.getElementById("status_saldo_group").style.display = 'block';
            } else if (metode === 'KREDIT') {
                document.getElementById("jumlah_uang_group").style.display = 'none'; // sembunyikan input jumlah uang
                document.getElementById("cek-fields-code").style.display = 'none';
                document.getElementById("cek-fields-date").style.display = 'none';
                document.getElementById("status_saldo_group").style.display = 'block';
            } else {
                document.getElementById("jumlah_uang_group").style.display = 'none'; // sembunyikan input jumlah uang
                document.getElementById("cek-fields-code").style.display = 'none';
                document.getElementById("cek-fields-date").style.display = 'none';
                document.getElementById("status_saldo_group").style.display = 'none';
            }
        });

        // Panggil function untuk menerapkan rule berdasarkan metode pembayaran yang sudah terpilih
        function applyPaymentMethodRule() {
            let metode = document.getElementById("metode_pembayaran").value;

            // Menyembunyikan atau menampilkan field berdasarkan metode pembayaran yang sudah terpilih
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

            if (metode === 'KREDIT' || metode === 'CEK') {
                document.getElementById("status_saldo_group").style.display = 'block';
            } else {
                document.getElementById("status_saldo_group").style.display = 'none';
            }
        }

        // Panggil function applyPaymentMethodRule saat halaman pertama kali dibuka
        applyPaymentMethodRule();

        // Tangani submit form pelunasan
        document.getElementById("pelunasanForm").addEventListener("submit", function (e) {
            e.preventDefault();

            let penjualanId = document.getElementById("penjualan_id").value;
            let jumlahUang = parseFloat(document.getElementById("jumlah_uang").value) || 0;
            let metodePembayaran = document.getElementById("metode_pembayaran").value;
            let kodeCek = document.getElementById("kode_cek").value;
            let tanggalCair = document.getElementById("tanggal_cair").value;
            let statusSaldo = document.getElementById("status_saldo").value;

            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

            let requestData = {
                jumlah_uang: jumlahUang,
                metode_pembayaran: metodePembayaran,
                kode_cek: kodeCek,
                tanggal_cair: tanggalCair,
                status_saldo: statusSaldo
            };

            fetch(`/penjualan/pelunasan/${penjualanId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify(requestData)
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(error => alert("Error: " + error.message));
        });
    });
</script>