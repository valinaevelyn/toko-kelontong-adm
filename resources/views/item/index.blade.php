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
            <div class="d-flex col mr-0  justify-content-end">
                <a href="{{ route('item.create') }}" class="btn btn-success">Tambah Item</a>
            </div>
        </div>

        <div class="col mt-4">
            <table class="table table-bordered table-primary text-center">
                <thead>
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Merk</th>
                        {{-- <th scope="col">UOM</th> --}}
                        <th scope="col">Harga Jual</th>
                        {{-- <th scope="col">Harga Beli</th> --}}
                        <th scope="col">Stock</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        @if ($item->count())
                            <tr class="table">
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->merek }}</td>
                                {{-- <td>{{ $item->uom }}</td> --}}
                                <td>{{ $item->harga_jual }}</td>
                                {{-- <td>{{ $item->harga_beli }}</td> --}}
                                <td> @if($item->stock_dus) <strong>{{ $item->stock_dus }} dus</strong><br> @endif
                                    @if($item->stock_rcg) <strong>{{ $item->stock_rcg }} renceng</strong><br> @endif
                                    @if($item->stock_pcs) <strong>{{ $item->stock_pcs }} pcs</strong> @endif
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