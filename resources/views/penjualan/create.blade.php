@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <h2>Tambah Penjualan</h2>

        @include('partials.danger')
        @include('partials.success')

        <form action="{{ route('penjualan.store') }}" method="POST">
            @csrf
            <div class="mb-4 mt-4">
                <label class="form-label">Nama Pembeli</label>
                <input type="text" name="nama_pembeli" class="form-control" required>
            </div>

            <h4>Items</h4>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Jumlah</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="itemTableBody">
                    <tr>
                        <td>
                            <select name="items[0][id]" class="form-select" required>
                                <option value="" selected disabled>Pilih Item</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" data-stock="{{ $item->stock }}">
                                        {{ $item->nama }} (Stok: {{ $item->stock }}, Rp
                                        {{ number_format($item->harga, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[0][jumlah]" class="form-control" min="1" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger remove-item" disabled>Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" id="addItem" class="btn btn-success">Tambah Item</button>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
            </div>
        </form>
    </div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let itemIndex = 1;

        document.getElementById("addItem").addEventListener("click", function () {
            let itemRow = `
                    <tr>
                        <td>
                            <select name="items[${itemIndex}][id]" class="form-select" required>
                                <option value="" selected disabled>Pilih Item</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" data-stock="{{ $item->stock }}">
                                        {{ $item->nama }} (Stok: {{ $item->stock }}, Rp
                                        {{ number_format($item->harga, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[${itemIndex}][jumlah]" class="form-control" min="1" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger remove-item">Hapus</button>
                        </td>
                    </tr>
                `;
            document.getElementById("itemTableBody").insertAdjacentHTML("beforeend", itemRow);
            itemIndex++;
        });

        document.getElementById("itemTableBody").addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-item")) {
                e.target.closest("tr").remove();
            }
        });
    });
</script>