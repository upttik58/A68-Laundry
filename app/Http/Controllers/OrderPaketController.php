<?php

namespace App\Http\Controllers;

use App\Models\JenisLaundry;
use App\Models\PaketMember;
use Illuminate\Http\Request;

class OrderPaketController extends Controller
{
    public function orderPaket()
    {
        $paketLaundry = PaketMember::where('user_id', auth()->user()->id)->with('paketLaundry.jenisLaundry')->get();
        return view('members.order_paket.order_paket',compact('paketLaundry'));
    }
}
