<?php

namespace App\Http\Controllers;

use App\Models\JenisLaundry;
use App\Models\PaketLaundry;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(){
        $jenisLaundry = JenisLaundry::all();
        $paketLaundry = PaketLaundry::all();
        return view('customers.main', compact('jenisLaundry', 'paketLaundry'));
    }
}
