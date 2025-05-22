<?php

namespace App\Http\Controllers;

use App\Models\JenisLaundry;
use App\Models\Orderan;
use App\Models\PaketLaundry;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $jenisLaundry = JenisLaundry::all();
        $paketLaundry = PaketLaundry::all();
        return view('customers.main', compact('jenisLaundry', 'paketLaundry'));
    }

    public function cekStatusCucian(Request $request)
    {
        $nomor_pesanan = $request->input('nomor_pesanan');
        $data = Orderan::with(['orderanDetail','jenisLaundry'])
            ->where('nomor_pesanan', $nomor_pesanan)
            ->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }
}
