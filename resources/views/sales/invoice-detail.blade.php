@extends('layouts.app')

@section('title', 'Invoice Penjualan')

@section('content')
<div class="main-content-table">
    <section class="section">
        <div class="margin-content">
            <div class="container-sm">
                <div class="section-header text-center mb-4">
                    <h1 class="fw-bold">Nomor Invoice: <strong>{{ $invoiceNumber }}</strong></h1>
                </div>
                <div class="invoice-container">
                    <div class="card shadow-sm p-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Nomor Invoice: <strong>{{ $invoiceNumber }}</strong></h5>
                                    <h5>Informasi Pelanggan</h5>
                                    <p><strong>Nama:</strong> {{ $memberName }}</p>
                                    <p><strong>Status:</strong> {{ $memberId ? 'Member' : 'Non-Member' }}</p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <h5>Tanggal Transaksi</h5>
                                    <p>{{ $createdAt->format('d F Y, H:i') }}</p>
                                </div>
                            </div>
                            
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Produk</th>
                                            <th>Harga</th>
                                            <th>Jumlah</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($productData as $key => $product)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $product['name'] }}</td>
                                            <td>Rp {{ number_format($product['price'], 0, ',', '.') }}</td>
                                            <td>{{ $product['quantity'] }}</td>
                                            <td>Rp {{ number_format($product['price'] * $product['quantity'], 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <p><strong>Total Pembayaran:</strong> Rp {{ number_format($totalPay, 0, ',', '.') }}</p>
                                    <p><strong>Total Belanja:</strong> Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                                    @if($discount > 0)
                                    <p><strong>Total Potongan:</strong> Rp {{ number_format($discount, 0, ',', '.') }}</p>
                                    <p><strong>Total Setelah Potongan:</strong> Rp {{ number_format($totalAmount - $discount, 0, ',', '.') }}</p>
                                    <p><strong>Kembalian:</strong> Rp {{ number_format($totalPay - $totalAmount + $discount, 0, ',', '.') }}</p>
                                    @else
                                    <p><strong>Kembalian:</strong> Rp {{ number_format($totalPay - $totalAmount, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="{{ route('sales.index') }}" class="btn btn-primary">Kembali ke Penjualan</a>
                                <button class="btn btn-success" onclick="window.print()">Cetak Invoice</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
