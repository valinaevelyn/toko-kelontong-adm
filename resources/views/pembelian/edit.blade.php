@extends('layouts.master')

@section('pembelianActive', 'active')
@section('content')
    <div class="container mt-4">
        <h2>Edit Pembelian</h2>

        @include('partials.danger')
        @include('partials.success')

        <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4 mt-4">
                <label class="form-label">Tanggal Pembelian</label>
                <input type="date" name="tanggal_pembelian" class="form-control"
                    value="{{ old('tanggal_pembelian', $pembelian->tanggal_pembelian) }}" required>
            </div>

            <div class="mb-4 mt-4">
                <label class="form-label">Nama Supplier</label>
                <input type="text" name="nama_supplier" class="form-control"
                    value="{{ old('nama_supplier', $pembelian->nama_supplier) }}" required>
            </div>

            <h4>Items</h4>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Jumlah</th>
                        <th>Harga Beli Satuan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                @php
                    $oldItems = old('items');
                    if (!$oldItems) {
                        $oldItems = $pembelian->pembelianDetails->map(function ($detail) {
                            return [
                                'id' => $detail->item_id,
                                'jumlah' => $detail->jumlah,
                                'harga_beli' => $detail->item->harga_beli,
                            ];
                        })->toArray();
                    }
                @endphp
                <tbody id="itemTableBody">
                    @foreach($oldItems as $i => $itemDetail)
                        <tr>
                            <td>
                                <select name="items[{{ $i }}][id]" class="form-select" required>
                                    <option value="" disabled {{ !isset($itemDetail['id']) ? 'selected' : '' }}>Pilih Item
                                    </option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ (old("items.$i.id", $itemDetail['id']) == $item->id) ? 'selected' : '' }}>
                                            {{ $item->nama }} (Stok: {{ $item->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][jumlah]" class="form-control" min="1"
                                    value="{{ old("items.$i.jumlah", $itemDetail['jumlah']) }}" required>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][harga_beli]" class="form-control" min="1"
                                    value="{{ old("items.$i.harga_beli", $itemDetail['harga_beli']) }}" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger remove-item" {{ $i == 0 ? 'disabled' : '' }}>Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <template id="itemRowTemplate">
                <tr>
                    <td>
                        <select name="items[__INDEX__][id]" class="form-select" required>
                            <option value="" selected disabled>Pilih Item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->nama }} (Stok: {{ $item->stock }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[__INDEX__][jumlah]" class="form-control" min="1" required>
                    </td>
                    <td>
                        <input type="number" name="items[__INDEX__][harga_beli]" class="form-control" min="1" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-item">Hapus</button>
                    </td>
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
        let itemIndex = {{ count($oldItems) }};
        const addItemButton = document.getElementById("addItem");
        const itemTableBody = document.getElementById("itemTableBody");
        const itemTemplate = document.getElementById("itemRowTemplate").innerHTML;

        addItemButton.addEventListener("click", function () {
            const newRow = itemTemplate.replaceAll('__INDEX__', itemIndex);
            itemTableBody.insertAdjacentHTML("beforeend", newRow);
            itemIndex++;
        });

        itemTableBody.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-item")) {
                e.target.closest("tr").remove();
            }
        });
    });
</script>