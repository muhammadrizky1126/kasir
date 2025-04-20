<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Ambil data penjualan 30 hari terakhir
        $salesData = Sale::where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Format data untuk chart
        $chartData = [
            'dates' => $salesData->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('d M');
            }),
            'totals' => $salesData->pluck('total')
        ];

        // Hitung total penjualan bulan ini
        $monthlySales = Sale::whereMonth('created_at', Carbon::now()->month)
            ->sum('total_amount');

        // Hitung total transaksi bulan ini
        $monthlyTransactions = Sale::whereMonth('created_at', Carbon::now()->month)
            ->count();

        return view('home', compact('chartData', 'monthlySales', 'monthlyTransactions'));
    }

    public function blank()
    {
        return view('layouts.blank-page');
    }
}
