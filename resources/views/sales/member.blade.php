@extends('layouts.app')

@section('title', 'Konfirmasi Penjualan')

@section('content')
<div class="main-content-table">
    <section class="section">
        <div class="margin-content">
            <div class="container-sm">
                <div class="section-header">
                    <h1>Konfirmasi Penjualan</h1>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="section-body">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form action="{{ route('sales.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Produk yang Dibeli</h5>
                                        <ul class="list-group">
                                            @foreach ($products as $key => $product)
                                                <li class="list-group-item">
                                                    <strong>{{ $key + 1 . '. ' . $product['name']}}</strong>
                                                    <br>Harga: Rp {{ number_format($product['price'], 0, ',', '.') }}
                                                    <br>Jumlah: {{ $product['quantity'] }}
                                                    <br>Subtotal: Rp {{ number_format($product['price'] * $product['quantity'], 0, ',', '.') }}
                                                </li>
                                                <hr>
                                            @endforeach
                                        </ul>
                                        <h5 class="mb-3">Total: Rp {{ number_format($totalAmount, 0, ',', '.') }}</h5>
                                        <input type="hidden" name="total_amount" value="{{ $totalAmount }}">
                                        <input type="hidden" name="total_pay" value="{{ $totalPay }}">
                                        <input type="hidden" name="member_id" value="{{ $member['id'] }}">
                                        <input type="hidden" name="product_data" value="{{ json_encode($products) }}">
                                    </div>

                                    <div class="col-md-6">
                                        <h5>Informasi Member</h5>
                                        <div class="form-group mb-3">
                                            <label for="is_member">Nama Member</label>
                                            <input type="text" class="form-control" name="member_name" value="{{ $member['name'] }}" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="total_pay">Jumlah Poin</label>
                                            <input type="text" class="form-control" id="total_pay" name="total_point" value="{{ $member['points'] }}" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="use_point" class="d-flex align-items-center">
                                                <input type="hidden" name="use_point" value="0">
                                                <input type="checkbox" name="use_point" value="1" id="use_point" class="styled-checkbox me-2 mr-1">
                                                Gunakan Poin
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('sales.create') }}" class="btn btn-secondary">Back</a>
                                    <button type="submit" class="btn btn-primary">Tambah Penjualan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#member_phone').select2({
            placeholder: "Pilih Member",
            width: '100%',
            allowClear: true
        });

        $('#is_member').on('change', function () {
            if ($(this).val() === "yes") {
                $('#member_selection').fadeIn();
            } else {
                $('#member_selection').fadeOut();
                $('#member_phone').val(null).trigger('change');
            }
        });

        $('#total_pay').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            $('#total_pay_numeric').val(value);
            if (value) {
                $(this).val(formatRupiah(value));
            } else {
                $(this).val('');
            }
        });

        $('form').on('submit', function() {
            let totalPay = $('#total_pay').val().replace(/\D/g, '');
            $('#total_pay_numeric').val(totalPay);
        });

        function formatRupiah(angka) {
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    });
</script>
@endpush
@endsection
