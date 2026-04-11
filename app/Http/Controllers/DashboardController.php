<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMember = Member::count();
        $totalTransaksi = Transaksi::count();

        return view('dashboard', compact('totalMember', 'totalTransaksi'));
    }
}