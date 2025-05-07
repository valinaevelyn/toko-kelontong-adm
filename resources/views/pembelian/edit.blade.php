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
                    value="{{ old('tanggal_pembelian', $pembelian->tanggal_pembelian) }}" readonly>
            </div>

            <div class="mb-4 mt-4">
                <label class="form-label">Nama Supplier</label>
                <input type="text" name="nama_supplier" class="form-control"
                    value="{{ old('nama_supplier', $pembelian->nama_supplier) }}" readonly>
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
                @php
                    $oldItems = old('items');
                    if (!$oldItems) {
                        $oldItems = $pembelian->pembelianDetails->map(function ($detail) {
                            return [
                                'id' => $detail->item_id,
                                'jumlah_dus' => $detail->jumlah_dus,
                                'jumlah_rcg' => $detail->jumlah_rcg,
                                'jumlah_pcs' => $detail->jumlah_pcs,
                                'dus_in_pcs' => $detail->item->dus_in_pcs,
                                'rcg_in_pcs' => $detail->item->rcg_in_pcs,
                                'harga_satuan' => $detail->harga_satuan,
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
                                                                    @php
                                                                        $total_stock = ($item->stock_dus * $item->dus_in_pcs) + ($item->stock_rcg * $item->rcg_in_ps) + $item->stock_pcs;
                                                                    @endphp
                                                                    <option value="{{ $item->id }}" {{ (old("items.$i.id", $itemDetail['id']) == $item->id) ? 'selected' : '' }}>
                                                                        {{ $item->nama }} (Stok: {{ $total_stock }})
                                                                    </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][jumlah_dus]" class="form-control" min="0"
                                            value="{{ old("items.$i.jumlah_dus", $itemDetail['jumlah_dus']) }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][jumlah_rcg]" class="form-control" min="0"
                                            value="{{ old("items.$i.jumlah_rcg", $itemDetail['jumlah_rcg']) }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][jumlah_pcs]" class="form-control" min="0"
                                            value="{{ old("items.$i.jumlah_pcs", $itemDetail['jumlah_pcs']) }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][dus_in_pcs]" class="form-control" min="0"
                                            value="{{ old("items.$i.dus_in_pcs", $itemDetail['dus_in_pcs']) }}">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][rcg_in_pcs]" class="form-control" min="0"
                                            value="{{ old("items.$i.rcg_in_pcs", $itemDetail['rcg_in_pcs']) }}">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][harga_satuan]" class="form-control" min="1"
                                            value="{{ old("items.$i.harga_satuan", $itemDetail['harga_satuan']) }}" required>
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
                                <option value="{{ $item->id }}">{{ $item->nama }} (Stok: {{ $item->stock }})</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[__INDEX__][jumlah_dus]" class="form-control" min="0" required>
                    </td>
                    <td>
                        <input type="number" name="items[__INDEX__][jumlah_rcg]" class="form-control" min="0" required>
                    </td>
                    <td>
                        <input type="number" name="items[__INDEX__][jumlah_pcs]" class="form-control" min="0" required>
                    </td>
                    <td>
                        <input type="number" name="items[__INDEX__][dus_in_pcs]" class="form-control" min="0">
                    </td>
                    <td>
                        <input type="number" name="items[__INDEX__][rcg_in_pcs]" class="form-control" min="0">
                    </td>
                    <td>
                        <input type="number" name="items[__INDEX__][harga_satuan]" class="form-control" min="1" required>
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

@section('scripts')
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
@endsection