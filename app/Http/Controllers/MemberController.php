<?php

namespace App\Http\Controllers;

use App\Models\PaketLaundry;
use App\Models\PaketMember;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class MemberController extends Controller
{
    public function index(){
        $paketMember = PaketMember::with('paketLaundry.jenisLaundry')->where('user_id',auth()->user()->id)->get();
        $paketLaundry = PaketLaundry::with('jenisLaundry')->get();
        return view('members.paket.paket', compact('paketMember', 'paketLaundry'));
    }

    public function store(Request $request){
        try {
            $request->validate([
                'paket_laundry_id' => 'required',
            ]);

            $paketLaundry = PaketLaundry::findOrFail($request->paket_laundry_id);

            $paketMember = PaketMember::create([
                'paket_laundry_id' => $request->paket_laundry_id,
                'user_id' => auth()->user()->id,
                'kg_terpakai' => 0,
                'kg_sisa' => $paketLaundry->berat,
                'status' => 'Belum Lunas',
                'kode_paket' => substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8) . '_' . time(),
            ]);

            // Set your Merchant Server Key
            Config::$serverKey = config('midtrans.serverKey');
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            Config::$isProduction = false;
            // Set sanitization on (default)
            Config::$isSanitized = true;
            // Set 3DS transaction for credit card to true
            Config::$is3ds = true;

            $params = array(
                'transaction_details' => array(
                    'order_id' => rand(),
                    'gross_amount' => $paketLaundry->harga,
                ),
                'customer_details' => array(
                    'first_name' => auth()->user()->nama,
                    'phone'      => auth()->user()->no_hp,
                    'address'    => auth()->user()->alamat,
                )
            );

            $snapToken = Snap::getSnapToken($params);

            $paketMember->update([
                'snap_token' => $snapToken,
            ]);

            return redirect('/paketLaundryMember')->with('success', 'Paket laundry berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $paketMember = PaketMember::findOrFail($id);
            $paketMember->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data gagal dihapus: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated =  $request->validate([
            'paket_laundry_id' => 'required|unique:paket_member',
        ]);

        try {
            PaketMember::where('id', $id)->update($validated);
            return redirect('/paketLaundryMember')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            return redirect('/paketLaundryMember')->with('error', 'Data gagal diubah: ' . $e->getMessage());
        }
    }

    public function bayarSuccess($id)
    {
        try {
            $paketMember = PaketMember::where('snap_token', $id)->firstOrFail();
            $paketMember->update([
                'status' => 'Lunas',
            ]);
            return redirect('/paketLaundryMember')->with('success', 'Pembayaran berhasil');
        } catch (\Exception $e) {
            return redirect('/paketLaundryMember')->with('error', 'Pembayaran gagal: ' . $e->getMessage());
        }
    }
}
