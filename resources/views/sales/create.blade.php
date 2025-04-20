@extends('layouts.app')

@section('title', 'Tambah Penjualan')

@section('content')
<div class="main-content-table">
    <section class="section">
        <div class="margin-content">
            <div class="container-sm">
                <div class="section-header">
                    <h1>Tambah Penjualan</h1>
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
                            <form action="{{ route('sales.confirmationStore') }}" method="POST">
                                @csrf
                                <div class="row">
                                    @foreach ($products as $product)
                                        <div class="col-md-4 d-flex align-items-stretch">
                                            <div class="card mb-3 w-100 d-flex flex-column">
                                                <div class="d-flex justify-content-center p-3" style="height: 250px; overflow: hidden;">
                                                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="object-fit: cover; height: 100%; width: 100%; max-height: 180px;">
                                                </div>
                                                <div class="card-body d-flex flex-column flex-grow-1 justify-content-between">
                                                    <h5 class="card-title text-center">{{ $product->name }}</h5>
                                                    <p class="card-text text-center">Harga: Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                                    <p class="card-text text-center">Stok: <span id="stock-{{ $product->id }}">{{ $product->quantity }}</span></p>
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary decrement" data-id="{{ $product->id }}">-</button>
                                                        <input type="number" name="quantities[{{ $product->id }}]" id="quantity-{{ $product->id }}" class="form-control text-center mx-2" style="width: 60px;" min="0" max="{{ $product->quantity }}" value="0">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary increment" data-id="{{ $product->id }}" data-stock="{{ $product->quantity }}">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">Kembali</a>
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Fungsi untuk update status tombol
        function updateButtonState(productId, currentValue) {
            const stock = parseInt(document.querySelector(`.increment[data-id="${productId}"]`).getAttribute('data-stock'));
            const incrementBtn = document.querySelector(`.increment[data-id="${productId}"]`);
            const decrementBtn = document.querySelector(`.decrement[data-id="${productId}"]`);

            // Disable tombol + jika sudah mencapai stok maksimum
            if (currentValue >= stock) {
                incrementBtn.disabled = true;
                incrementBtn.classList.add('disabled');
            } else {
                incrementBtn.disabled = false;
                incrementBtn.classList.remove('disabled');
            }

            // Disable tombol - jika nilai 0
            if (currentValue <= 0) {
                decrementBtn.disabled = true;
                decrementBtn.classList.add('disabled');
            } else {
                decrementBtn.disabled = false;
                decrementBtn.classList.remove('disabled');
            }
        }

        // Inisialisasi status tombol saat pertama kali load
        document.querySelectorAll(".increment").forEach(button => {
            const productId = button.getAttribute("data-id");
            const input = document.getElementById("quantity-" + productId);
            updateButtonState(productId, parseInt(input.value));
        });

        // Event listener untuk tombol +
        document.querySelectorAll(".increment").forEach(button => {
            button.addEventListener("click", function () {
                let productId = this.getAttribute("data-id");
                let input = document.getElementById("quantity-" + productId);
                let stock = parseInt(this.getAttribute("data-stock"));

                if (input && parseInt(input.value) < stock) {
                    input.value = parseInt(input.value) + 1;
                    updateButtonState(productId, parseInt(input.value));
                }
            });
        });

        // Event listener untuk tombol -
        document.querySelectorAll(".decrement").forEach(button => {
            button.addEventListener("click", function () {
                let productId = this.getAttribute("data-id");
                let input = document.getElementById("quantity-" + productId);

                if (input && parseInt(input.value) > 0) {
                    input.value = parseInt(input.value) - 1;
                    updateButtonState(productId, parseInt(input.value));
                }
            });
        });

        // Event listener untuk input manual
        document.querySelectorAll("input[type='number']").forEach(input => {
            input.addEventListener("change", function() {
                const productId = this.id.split('-')[1];
                const stock = parseInt(document.querySelector(`.increment[data-id="${productId}"]`).getAttribute('data-stock'));
                let value = parseInt(this.value);

                // Validasi jika input melebihi stok
                if (value > stock) {
                    this.value = stock;
                    value = stock;
                } else if (value < 0) {
                    this.value = 0;
                    value = 0;
                }

                updateButtonState(productId, value);
            });
        });
    });
</script>

<style>
    .disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

@endsection
