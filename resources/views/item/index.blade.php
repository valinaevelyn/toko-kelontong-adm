@extends('layouts.master')
@section('itemActive', 'active')
@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 fs-1 fw-3">
                Persediaan Item
            </div>
        </div>
        <hr>
        <div class="col">
            @include('partials.danger')
            @include('partials.success')

            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="{{ route('item.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Cari nama atau merk..."
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </form>
                </div>

                <div class="d-flex col justify-content-end">
                    <a href="{{ route('item.create') }}" class="btn btn-success">Tambah Item</a>
                </div>
            </div>
        </div>

        <div class="col mt-4">
            <table class="table table-bordered table-primary ">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">Nama</th>
                        <th scope="col" class="text-center">Merk</th>
                        <th scope="col" class="text-center">Kategori</th>
                        {{-- <th scope="col">UOM</th> --}}
                        <th scope="col" class="text-center">Harga Jual</th>
                        {{-- <th scope="col">Harga Beli</th> --}}
                        <th scope="col" class="text-center">Stock</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        @if ($item->count())
                            <tr class="table align-middle">
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->merek }}</td>
                                <td>{{ $item->kategori }}</td>
                                {{-- <td>{{ $item->uom }}</td> --}}
                                {{-- <td>{{ $item->uom }}</td> --}}
                                <td>{{ $item->harga_jual }}</td>
                                {{-- <td>{{ $item->harga_beli }}</td> --}}
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <select class="form-select form-select-sm satuan-select" onchange="updateStockDisplay(this)"
                                            style="width: 120px;">
                                            <option value="dus">Dus</option>
                                            <option value="rcg">Renceng</option>
                                            <option value="pcs" selected>PCS</option>
                                        </select>
                                        <span class="stock-display fs-6">
                                            {{ $item->stock_pcs }} pcs
                                        </span>
                                    </div>
                                    <div class="d-none stock-dus">
                                        {{ $item->stock_dus ?? 0 }} dus
                                    </div>
                                    <div class="d-none stock-rcg">
                                        {{ $item->stock_rcg ?? 0 }} renceng
                                    </div>
                                    <div class="d-none stock-pcs">
                                        {{ $item->stock_pcs ?? 0 }} pcs
                                    </div>
                                </td>


                                <td>
                                    <div class="dropdown d-flex justify-content-center">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                            id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a class="dropdown-item" href="{{ route('item.edit', $item->id) }}">Edit</a>
                                            </li>
                                            <li>
                                                <form onclick="confirm('are you sure?')"
                                                    action="{{ route('item.destroy', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item">Delete</a>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr class="table-secondary">
                                <td colspan="6">Tidak ada item tersedia.</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            {{ $items->links() }}
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
    </div>

@endsection

<script>
    function updateStockDisplay(selectElement) {
        const parent = selectElement.closest('td');
        const stockDisplay = parent.querySelector('.stock-display');
        const selectedUnit = selectElement.value;

        const dusStock = parent.querySelector('.stock-dus')?.textContent.trim();
        const rcgStock = parent.querySelector('.stock-rcg')?.textContent.trim();
        const pcsStock = parent.querySelector('.stock-pcs')?.textContent.trim();

        if (selectedUnit === 'dus') {
            stockDisplay.textContent = dusStock;
        } else if (selectedUnit === 'rcg') {
            stockDisplay.textContent = rcgStock;
        } else if (selectedUnit === 'pcs') {
            stockDisplay.textContent = pcsStock;
        }
    }
</script>