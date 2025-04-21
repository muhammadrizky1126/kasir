<?php
use Illuminate\Support\Facades\DB;
?>

@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="main-content" style="margin-left: 220px; padding: 20px;">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                <hr class="mt-2 mb-4" style="border-color: #e3e6f0;">
            </div>
        </div>

        <!-- Cards Section -->
        <div class="row mb-4">
            <!-- Card Total Penjualan Bulan Ini -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Penjualan Bulan Ini</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Total Transaksi Bulan Ini -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Transaksi Bulan Ini</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyTransactions }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-receipt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Menu Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Menu Cepat</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                    <a href="{{ route('products.index') }}" class="text-decoration-none mr-2">Produk</a>
                                    <a href="{{ route('sales.index') }}" class="text-decoration-none mr-2">Penjualan</a>
                                    <a href="{{ route('members.index') }}" class="text-decoration-none">Member</a>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-bars fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="row">
            <!-- Sales Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Grafik Penjualan 30 Hari Terakhir</h6>
                    </div>
                    <div class="card-body" style="height: 400px;">
                        <div class="chart-area">
                            <canvas id="salesChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->

        </div>
    </div>
</div>

<!-- Script untuk Chart -->
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari controller
        const chartData = @json($chartData);

        // Konfigurasi chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.dates,
                datasets: [{
                    label: 'Total Penjualan',
                    data: chartData.totals,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) {
                                return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'Rp ' + context.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    .main-content {
        transition: margin-left 0.3s;
        background-color: #f8f9fc;
        min-height: 100vh;
    }

    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .activity-item {
        border-left: 3px solid #4e73df;
        padding-left: 15px;
        margin-bottom: 15px;
    }

    .activity-date {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.8rem;
    }

    .activity-content {
        font-size: 0.9rem;
        color: #343a40;
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
        }

        .col-xl-4, .col-xl-8 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }da
</style>
@endsection
@endsection
