<?php

namespace App\Http\Controllers;

use App\Models\JenisLaundry;
use App\Models\Orderan;
use App\Models\OrderanDetail;
use App\Models\PaketMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class OrderanStafController extends Controller
{
    public function offline()
    {
        $jenisLaundry = JenisLaundry::all();
        $orderan = Orderan::with(['orderanDetail', 'jenisLaundry'])->where('is_offline', '1')->get();
        return view('staffs.offline.offline', compact('jenisLaundry', 'orderan'));
    }

    public function storeOffline(Request $request)
    {
        $validatedData = $request->validate([
            'nama'           => 'required',
            'no_hp'          => 'required',
            'alamat'         => 'required',
            'jenis_laundry'  => 'required',
            'berat'          => 'required',
            'harga'          => 'required',
            'pembayaran'     => 'required',
        ]);

        try {
            DB::beginTransaction();

            $orderan = Orderan::create([
                'nomor_pesanan' => substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8) . '_' . time(),
                'jenis_laundry' => $validatedData['jenis_laundry'],
                'berat'         => $validatedData['berat'],
                'harga'         => $validatedData['harga'],
                'pembayaran'    => $validatedData['pembayaran'],
                'status'        => 'Belum Lunas',
                'status_cucian' => 'Orderan Masuk',
                'is_offline'    => '1',
                'is_paket'      => '0'
            ]);

            OrderanDetail::create([
                'orderan_id' => $orderan->id,
                'nama'       => $validatedData['nama'],
                'no_hp'      => $validatedData['no_hp'],
                'alamat'     => $validatedData['alamat'],
            ]);

            if ($validatedData['pembayaran'] == 'Transfer') {

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
                        'gross_amount' => $validatedData['harga'],
                    ),
                    'customer_details' => array(
                        'first_name' => $validatedData['nama'],
                        'phone'      => $validatedData['no_hp'],
                        'address'    => $validatedData['alamat'],
                    )
                );

                $snapToken = Snap::getSnapToken($params);

                $orderan->update([
                    'snap_token' => $snapToken,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Orderan offline berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan orderan offline: ' . $e->getMessage());
        }
    }

    public function destroyOffline($id)
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

    public function updateOffline(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama'           => 'required',
            'no_hp'          => 'required',
            'alamat'         => 'required',
            'jenis_laundry'  => 'required',
            'berat'          => 'required',
            'harga'          => 'required',
            'pembayaran'     => 'required',
        ]);

        try {
            DB::beginTransaction();

            $orderan = Orderan::findOrFail($id);
            $orderan->update([
                'nomor_pesanan' => substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8) . '_' . time(),
                'jenis_laundry' => $validatedData['jenis_laundry'],
                'berat'         => $validatedData['berat'],
                'harga'         => $validatedData['harga'],
                'pembayaran'    => $validatedData['pembayaran'],
                'status'        => 'Belum Lunas'
            ]);

            OrderanDetail::where('orderan_id', $id)->update([
                'nama'       => $validatedData['nama'],
                'no_hp'      => $validatedData['no_hp'],
                'alamat'     => $validatedData['alamat'],
            ]);

            if ($validatedData['pembayaran'] == 'Transfer') {

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
                        'gross_amount' => $validatedData['harga'],
                    ),
                    'customer_details' => array(
                        'first_name' => $validatedData['nama'],
                        'phone'      => $validatedData['no_hp'],
                        'address'    => $validatedData['alamat'],
                    )
                );

                $snapToken = Snap::getSnapToken($params);

                $orderan->update([
                    'snap_token' => $snapToken,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Orderan offline berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui orderan offline: ' . $e->getMessage());
        }
    }

    public function bayarOffline($id)
    {
        try {
            $orderan = Orderan::findOrFail($id);
            $orderan->update([
                'status' => 'Sudah Lunas',
                'status_cucian' => 'Sedang Dicuci'
            ]);

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

    public function bayarOfflineSelesai($id)
    {
        try {
            $orderan = Orderan::findOrFail($id);
            $orderan->update([
                'status_cucian' => 'Sudah Selesai'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proses selesai, cucian sudah selesai.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proses gagal, cucian belum selesai: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bayarOfflineDiambil($id)
    {
        try {
            $orderan = Orderan::findOrFail($id);
            $orderan->update([
                'status_cucian' => 'Sudah Diambil'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proses selesai, cucian sudah diambil.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proses gagal, cucian belum diambil: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bayarOfflineSuccess($id)
    {
        try {
            $orderan = Orderan::where('snap_token', $id)->first();
            $orderan->update([
                'status' => 'Sudah Lunas',
                'status_cucian' => 'Sedang Dicuci'
            ]);

            return redirect('/offline')->with('success', 'Pembayaran berhasil dilakukan.');
        } catch (\Exception $e) {
            return redirect('/offline')->with('error', 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function online()
    {
        $jenisLaundry = JenisLaundry::all();
        $orderan = Orderan::with(['orderLocation', 'jenisLaundry', 'user'])
            ->where('is_offline', '0')
            ->where('status_cucian', '!=', 'Orderan Masuk')
            ->get();
        return view('staffs.online.online', compact('orderan', 'jenisLaundry'));
    }

    public function jemput($id)
    {
        try {
            $orderan = Orderan::findOrFail($id);
            $orderan->update([
                'status_cucian' => 'Menunggu Timbangan'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cucian Sedang Dijempt'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proses gagal, cucian belum dijemput: ' . $e->getMessage()
            ], 500);
        }
    }

    public function berat(Request $request)
    {
        try {
            $orderan = Orderan::findOrFail($request->id);
            if ($orderan->is_paket == 0) {
                $orderan->update([
                    'status_cucian' => 'Menunggu Pembayaran',
                    'berat'         => $request->berat,
                    'harga'         => $request->harga
                ]);
            } else {
                $paketMember = PaketMember::with('paketLaundry')->whereHas('paketLaundry', function ($query) use ($orderan) {
                    $query->where('jenis_laundry_id', $orderan->jenis_laundry);
                })->first();

                if ($paketMember->kg_sisa > 0 && $request->berat <= $paketMember->kg_sisa) {
                    $orderan->update([
                        'status_cucian' => 'Sedang Dicuci',
                        'berat'         => $request->berat,
                        'harga'         => $request->harga
                    ]);

                    $paketMember->kg_terpakai = $request->berat;
                    $paketMember->kg_sisa = $paketMember->paketLaundry->berat - $request->berat;
                    $paketMember->save();
                } else {
                    return redirect('/online')->with('error', 'Paket Laundry Anda Sudah Habis');
                }
            }

            return redirect('/online')->with('success', 'Input Timbangan Berhasil');
        } catch (\Exception $e) {
            return redirect('/online')->with('error', 'Terjadi kesalahan saat memproses data.' . $e->getMessage());
        }
    }

    public function antar($id)
    {
        try {
            $orderan = Orderan::findOrFail($id);
            $orderan->update([
                'status_cucian' => 'Cucian Dalam Perjalanan'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cucian Sedang Diantar'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proses gagal, cucian belum diantar: ' . $e->getMessage()
            ], 500);
        }
    }
}
