@extends('layouts.master')

@section('pembelianActive', 'active')
@section('content')
    <div class="container mt-4">
        <h2>Tambah Pembelian</h2>

        @include('partials.danger')
        @include('partials.success')

        <form action="{{ route('pembelian.store') }}" method="POST">
            @csrf
            <div class="mb-4 mt-4">
                <label class="form-label">Nama Supplier</label>
                <input type="text" name="nama_supplier" class="form-control" required>
            </div>

            <h4>Items</h4>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Jumlah DUS</th>
                        <th>Jumlah RCG</th>
                        <th>Jumlah PCS</th>
                        <th>PCS per DUS</th>
                        <th>PCS per RCG</th>
                        <th>Harga Beli Satuan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="itemTableBody">
                    <tr>
                        <td>
                            <select name="items[0][id]" class="form-select" required>
                                <option value="" selected disabled>Pilih Item</option>
                                @foreach($items as $item)
                                                            @php
                                                                $total_stock = ($item->stock_dus * $item->dus_in_pcs) + ($item->stock_rcg * $item->rcg_in_ps) + $item->stock_pcs;
                                                            @endphp
                                                            <option value="{{ $item->id }}" data-pcs-dus="{{ $item->dus_in_pcs }}"
                                                                data-pcs-rcg="{{ $item->rcg_in_ps }}">
                                                                {{ $item->nama }} (Stok: {{ $total_stock }})
                                                            </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[0][stock_dus]" class="form-control" min="0" value="0" required>
                        </td>
                        <td>
                            <input type="number" name="items[0][stock_rcg]" class="form-control" min="0" value="0" required>
                        </td>
                        <td>
                            <input type="number" name="items[0][stock_pcs]" class="form-control" min="0" value="0" required>
                        </td>
                        <td>
                            <input type="number" name="items[0][dus_in_pcs]" class="form-control" min="0" value="0">
                        </td>
                        <td>
                            <input type="number" name="items[0][rcg_in_pcs]" class="form-control" min="0" value="0">
                        </td>
                        <td>
                            <input type="number" name="items[0][harga_satuan]" class="form-control" min="1" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger remove-item">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <template id="templateRow">
                <tr>
                    <td>
                        <select name="items[__INDEX__][id]" class="form-select" required>
                            <option value="" selected disabled>Pilih Item</option>
                            @foreach($items as $item)
                                                    @php
                                                        $total_stock = ($item->stock_dus * $item->dus_in_pcs) + ($item->stock_rcg * $item->rcg_in_ps) + $item->stock_pcs;
                                                    @endphp
                                                    <option value="{{ $item->id }}" data-pcs-dus="{{ $item->dus_in_pcs }}"
                                                        data-pcs-rcg="{{ $item->rcg_in_ps }}">
                                                        {{ $item->nama }} (Stok: {{ $total_stock }})
                                                    </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="items[__INDEX__][stock_dus]" class="form-control" min="0" value="0"
                            required></td>
                    <td><input type="number" name="items[__INDEX__][stock_rcg]" class="form-control" min="0" value="0"
                            required></td>
                    <td><input type="number" name="items[__INDEX__][stock_pcs]" class="form-control" min="0" value="0"
                            required></td>
                    <td><input type="number" name="items[__INDEX__][dus_in_pcs]" class="form-control" min="0" value="0">
                    </td>
                    <td><input type="number" name="items[__INDEX__][rcg_in_pcs]" class="form-control" min="0" value="0">
                    </td>
                    <td><input type="number" name="items[__INDEX__][harga_satuan]" class="form-control" min="1" required>
                    </td>
                    <td><button type="button" class="btn btn-danger remove-item">Hapus</button></td>
                </tr>
            </template>

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

        // Fungsi untuk mengupdate nilai PCS per DUS dan RCG saat item dipilih
        window.updatePcsValues = function (selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const pcsPerDus = selectedOption.getAttribute('data-pcs-dus');
            const pcsPerRcg = selectedOption.getAttribute('data-pcs-rcg');

            const row = selectElement.closest('tr');
            row.querySelector('input[name*="[dus_in_pcs]"]').value = pcsPerDus;
            row.querySelector('input[name*="[rcg_in_pcs]"]').value = pcsPerRcg;
        };

        // Tambahkan baris baru dari template
        document.getElementById("addItem").addEventListener("click", function () {
            const template = document.getElementById("templateRow").innerHTML;
            const newRow = template.replace(/__INDEX__/g, itemIndex);
            document.getElementById("itemTableBody").insertAdjacentHTML("beforeend", newRow);
            itemIndex++;
        });

        // Hapus baris item
        document.getElementById("itemTableBody").addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-item")) {
                e.target.closest("tr").remove();
            }
        });
    });
</script>