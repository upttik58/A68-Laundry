<?php

namespace App\Http\Controllers;

use App\Models\JenisLaundry;
use App\Models\Orderan;
use App\Models\OrderLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class OrderLangsungController extends Controller
{
    public function orderLangsung()
    {
        $jenisLaundry = JenisLaundry::all();
        $orderan = Orderan::with(['jenisLaundry', 'orderLocation'])->where('user_id', auth()->user()->id)->get();
        return view('members.order_langsung.order_langsung', compact('jenisLaundry', 'orderan'));
    }

    public function orderLangsungStore(Request $request)
    {
        try {
            $request->validate([
                'jenis_laundry_id' => 'required',
            ]);

            Orderan::create([
                'nomor_pesanan'    => substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8) . '_' . time(),
                'jenis_laundry'    => $request->jenis_laundry_id,
                'user_id'          => auth()->user()->id,
                'pembayaran'       => 'Transfer',
                'status'           => 'Belum Lunas',
                'status_cucian'    => 'Orderan Masuk',
                'is_offline'       => '0',
            ]);

            return redirect()->back()->with('success', 'Order berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function orderLangsungUpdate(Request $request, $id)
    {
        try {
            $request->validate([
                'jenis_laundry_id' => 'required',
            ]);

            $orderan = Orderan::findOrFail($id);
            $orderan->update([
                'jenis_laundry' => $request->jenis_laundry_id,
            ]);

            return redirect()->back()->with('success', 'Order berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function orderLangsungDestroy($id)
    {
        try {
            $orderan = Orderan::findOrFail($id);
            $orderan->delete();

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

    public function orderLangsungBayarSuccess($id)
    {
        try {
            $orderan = Orderan::where('snap_token', $id)->first();
            $orderan->update([
                'status' => 'Sudah Lunas',
                'status_cucian' => 'Sedang Dicuci'
            ]);

            return redirect('/orderLangsung')->with('success', 'Pembayaran berhasil dilakukan.');
        } catch (\Exception $e) {
            return redirect('/orderLangsung')->with('error', 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function setLocation($id)
    {
        return view('members.set_location', compact('id'));
    }

    public function search(Request $request)
    {
        $query = $request->query('q');

        // Jika query kosong, kembalikan array kosong
        if (!$query) {
            return response()->json([]);
        }

        // Gunakan hash sebagai cache key agar aman dan unik
        $cacheKey = 'geocode:' . md5($query);

        // Ambil hasil dari cache, atau lakukan request ke Nominatim
        $results = Cache::remember($cacheKey, 600, function () use ($query) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'MyLaravelApp/1.0 (a68laundry@gmail.com)',
                ])->get('https://nominatim.openstreetmap.org/search', [
                    'format' => 'json',
                    'q' => $query,
                    'addressdetails' => 1,
                    'limit' => 5,
                ]);

                // Cek apakah respon sukses dan memiliki data
                if ($response->successful()) {
                    return $response->json();
                }

                return [];
            } catch (\Exception $e) {
                // Jika terjadi error, log dan kembalikan array kosong
                Log::error('Geocoding error: ' . $e->getMessage());
                return [];
            }
        });

        return response()->json($results);
    }

    public function setLocationInsertOrUpdate(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'latitudeInput' => 'required',
                'longitudeInput' => 'required',
                'distanceInput' => 'required',
            ]);

            Orderan::where('id', $request->id)->update([
                'status_cucian' => 'Menunggu Dijemput',
            ]);

            OrderLocation::updateOrCreate(
                ['order_id' => $request->id],
                [
                    'order_id' => $request->id,
                    'latitude' => $request->latitudeInput,
                    'longitude' => $request->longitudeInput,
                    'distance' => $request->distanceInput,
                ]
            );

            return redirect('/orderLangsung')->with('success', 'Lokasi berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect('/orderLangsung')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function orderLangsungBayar(Request $request)
    {
        try {
            $orderan = Orderan::with('orderLocation')->findOrFail($request->id);
            $totalPembayaran = $orderan->harga + ($orderan->orderLocation->distance * 5000);

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
                    'gross_amount' => $totalPembayaran
                ),
                'customer_details' => array(
                    'first_name' => auth()->user()->nama,
                    'phone'      => auth()->user()->no_hp,
                    'address'    => auth()->user()->alamat,
                )
            );

            $snapToken = Snap::getSnapToken($params);

            $orderan->update([
                'snap_token' => $snapToken,
            ]);

            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function selesai($id)
    {
        try {
            $orderan = Orderan::findOrFail($id);
            $orderan->update([
                'status_cucian' => 'Cucian Diterima'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cucian Sudah Diterima'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proses gagal, cucian belum diterima: ' . $e->getMessage()
            ], 500);
        }
    }
}
