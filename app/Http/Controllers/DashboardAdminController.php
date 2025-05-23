<?php

namespace App\Http\Controllers;

use App\Models\Orderan;
use App\Models\PaketMember;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index(){
        $totalPendapatanPaket = PaketMember::join('paket_laundry', 'paket_member.paket_laundry_id', '=', 'paket_laundry.id')
            ->sum('paket_laundry.harga');
        $totalPendapatan = Orderan::where('is_paket','0')->sum('harga') + $totalPendapatanPaket;
        $totalOrderan = Orderan::count();
        $totalMember = User::count();

        return view('owners.dashboard.dashboard', compact('totalPendapatan', 'totalOrderan', 'totalMember'));
    }
}
