@extends('layouts.master')
@section('penjualanActive', 'active')
@section('content')
    <div class="container mt-4">
        <h2>Edit Penjualan</h2>

        @include('partials.danger')
        @include('partials.success')

        <form action="{{ route('penjualan.update', $penjualan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4 mt-4">
                <label class="form-label">Tanggal Penjualan</label>
                <input type="date" name="tanggal_penjualan" class="form-control"
                    value="{{ old('tanggal_penjualan', $penjualan->tanggal_penjualan) }}" required>
            </div>

            <div class="mb-4 mt-4">
                <label class="form-label">Nama Pembeli</label>
                <input type="text" name="nama_pembeli" class="form-control"
                    value="{{ old('nama_pembeli', $penjualan->nama_pembeli) }}" required>
            </div>

            <h4>Items</h4>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>UOM</th>
                        <th>Jumlah </th>
                        <th>Harga Jual</th>
                        <th>Action</th>
                    </tr>
                </thead>
                @php
                    $oldItems = old('items');
                    if (!$oldItems) {
                        $oldItems = $penjualan->penjualanDetails->map(function ($detail) {
                            return [
                                'id' => $detail->item_id,
                                'jumlah_dus' => $detail->jumlah_dus,
                                'jumlah_rcg' => $detail->jumlah_rcg,
                                'jumlah_pcs' => $detail->jumlah_pcs,
                                'dus_in_pcs' => $detail->item->dus_in_pcs,
                                'rcg_in_pcs' => $detail->item->rcg_in_pcs,
                                'harga_satuan' => $detail->harga_satuan,
                                'satuan' => $detail->jumlah_dus > 0 ? 'dus' : ($detail->jumlah_rcg > 0 ? 'rcg' : 'pcs'), // atau bisa juga diisi default seperti 'pcs'
                'jumlah' => $detail->jumlah_dus ?: ($detail->jumlah_rcg ?: $detail->jumlah_pcs),
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
                                        <select name="items[{{ $i }}][satuan]" class="form-select" required>
                                            <option value="" disabled selected>Pilih Satuan</option>
                                            <option value="dus" {{ old("items.$i.satuan", $itemDetail['satuan']) == 'dus' ? 'selected' : '' }}>DUS</option>
                                            <option value="rcg" {{ old("items.$i.satuan", $itemDetail['satuan']) == 'rcg' ? 'selected' : '' }}>RCG</option>
                                            <option value="pcs" {{ old("items.$i.satuan", $itemDetail['satuan']) == 'pcs' ? 'selected' : '' }}>PCS</option>
                                        </select>

                                    <td>
                                        <input type="number" name="items[{{ $i }}][jumlah]" class="form-control" min="0"
                                            value="{{ old("items.$i.jumlah", $itemDetail['jumlah']) }}" required>
                                    </td>
                                    <td>
                                            <input type="number" name="items[{{ $i }}][harga_satuan]" class="form-control" min="0"
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
                        <select name="items[__INDEX__][satuan]" class="form-select" required>
                            <option value="" selected disabled>Pilih Satuan</option>
                            <option value="dus">DUS</option>
                            <option value="rcg">RCG</option>
                            <option value="pcs">PCS</option>
                        </select>

                    </td>

                    <td>
                        <input type="number" name="items[__INDEX__][jumlah]" class="form-control" min="0" required>

                    </td>

                    <td>
                        <input type="number" name="items[__INDEX__][harga_satuan]" class="form-control" min="0" required>

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