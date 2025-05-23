<?php

namespace App\Http\Controllers;

use App\Models\JenisLaundry;
use App\Models\Orderan;
use App\Models\OrderLocation;
use App\Models\PaketMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderPaketController extends Controller
{
    public function orderPaket()
    {
        $paketLaundry = PaketMember::where('user_id', auth()->user()->id)->with('paketLaundry.jenisLaundry')->get();
        $orderan = Orderan::with(['jenisLaundry', 'orderLocation'])
            ->where('user_id', auth()->user()->id)
            ->where('is_paket', '1')
            ->get();
        return view('members.order_paket.order_paket', compact('paketLaundry', 'orderan'));
    }

    public function orderPaketStore(Request $request)
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
                'status'           => 'Sudah Lunas',
                'status_cucian'    => 'Orderan Masuk',
                'is_offline'       => '0',
                'is_paket'         => '1'
            ]);

            return redirect()->back()->with('success', 'Order berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function orderPaketUpdate(Request $request, $id)
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

    public function orderPaketDestroy($id)
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

    public function setLocation($id)
    {
        return view('members.order_paket.set_location', compact('id'));
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

            return redirect('/orderPaket')->with('success', 'Lokasi berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect('/orderPaket')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
