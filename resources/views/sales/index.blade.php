@extends('layouts.app')

@section('title', 'Penjualan')

@push('style')
    <style>
        .btn-export-excel {
            background-color: #1cc88a;
            color: white;
            border-radius: 5px;
            padding: 8px 15px;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-export-excel:hover {
            background-color: #17a673;
            color: white;
        }
        .btn-export-excel i {
            margin-right: 5px;
        }
    </style>
@endpush

@section('content')
<div class="main-content-table">
    <section class="section">
        <div class="margin-content">
            <div class="container-sm">
                <div class="section-header">
                    <h1>Penjualan</h1>
                </div>
                <div class="section-body">
                    <div class="table-responsive">
                        <div class="row mb-3">
                            <div class="col-md-12 d-flex justify-content-between align-items-center">
                                <form action="{{ route('sales.index') }}" method="GET" class="d-flex" style="max-width: 100%;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control rounded" placeholder="Search" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary rounded ml-2" type="submit">Search</button>
                                        </div>
                                    </div>
                                </form>
                                <div class="d-flex">
                                    @if(Auth::user()->role == 'user')
                                        <a href="{{ route('sales.create') }}" class="btn btn-success ml-2 p-2">
                                            <i class="fas fa-plus"></i> Tambah Penjualan
                                        </a>
                                    @else
                                        <a href="{{ route('sales.export') }}" class="btn-export-excel ml-2" id="exportExcelBtn">
                                            <i class="fas fa-file-excel"></i> Export Excel
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered" style="background-color: #f3f3f3">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Tanggal Penjualan</th>
                                    <th>Total Harga</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $index => $item)
                                <tr>
                                    <td>{{ $sales->firstItem() + $index }}</td>
                                    <td>{{ $item->customer_name }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ 'Rp ' . number_format($item->total_amount, 0, ',', '.') }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('sales.invoice', $item->id) }}" class="btn btn-info">
                                            <i class="fas fa-download"></i> Unduh Bukti
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $sales->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Handle export Excel dengan feedback
    const exportBtn = document.getElementById('exportExcelBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Tampilkan loading
            const originalHtml = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan export...';
            this.classList.add('disabled');

            // Lakukan request export
            fetch(this.href, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal melakukan export');
                }
                return response.blob();
            })
            .then(blob => {
                // Buat link download
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'laporan_penjualan_' + new Date().toISOString().slice(0, 10) + '.xlsx';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal melakukan export. Silakan coba lagi atau hubungi administrator.');
            })
            .finally(() => {
                // Kembalikan tombol ke state semula
                this.innerHTML = originalHtml;
                this.classList.remove('disabled');
            });
        });
    }
});
</script>
@endpush
