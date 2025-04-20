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
                                            dd)
                                                <li class="list-group-item">
                                                    <strong>{{ $key + 1 . '. ' . $product->name}}</strong>
                                                    <br>Harga: Rp {{ number_format($product->price, 0, ',', '.') }}
                                                    <br>Jumlah: {{ $filteredQuantities[$product->id] }}
                                                    <br>Subtotal: Rp {{ number_format($product->price * $filteredQuantities[$product->id], 0, ',', '.') }}
                                                </li>
                                                <hr>
                                            @endforeach
                                        </ul>
                                        <h5 class="mb-3">Total: Rp {{ number_format($totalAmount, 0, ',', '.') }}</h5>
                                        <input type="hidden" name="total_amount" value="{{ $totalAmount }}">
                                        <input type="hidden" name="product_data" value="{{ json_encode($products->map(function ($product) use ($filteredQuantities) {
                                            return [
                                                'id' => $product->id,
                                                'name' => $product->name,
                                                'price' => $product->price,
                                                'quantity' => $filteredQuantities[$product->id] ?? 0,
                                                'subtotal' => $product->price * ($filteredQuantities[$product->id] ?? 0),
                                            ];
                                        })) }}">
                                    </div>

                                    <div class="col-md-6">
                                        <h5>Informasi Member</h5>
                                        <div class="form-group mb-3">
                                            <label for="is_member">Member atau Bukan</label>
                                            <select class="form-control" id="is_member" name="is_member" required>
                                                <option value="">Pilih</option>
                                                <option value="yes">Member</option>
                                                <option value="no">Bukan Member</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3" id="member_selection" style="display: none;">
                                            <label for="member_phone">Pilih Member (Berdasarkan Nomor Telepon)</label>
                                            <br>
                                            <select class="form-control select2" id="member_phone" name="member_id">
                                                <option value="">Pilih Member</option>
                                                @foreach ($members as $member)
                                                    <option value="{{ $member->id }}">{{ $member->phone_number }} - {{ $member->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="total_pay">Jumlah Bayar</label>
                                            <input type="text" class="form-control" id="total_pay" value="">
                                            <input type="hidden" id="total_pay_numeric" name="total_pay">
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
