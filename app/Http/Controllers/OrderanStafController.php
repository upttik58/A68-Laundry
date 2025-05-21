<?php

namespace App\Http\Controllers;

use App\Models\JenisLaundry;
use App\Models\Orderan;
use App\Models\OrderanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderanStafController extends Controller
{
    public function offline()
    {
        $jenisLaundry = JenisLaundry::all();
        $orderan = Orderan::with(['orderanDetail', 'jenisLaundry'])->get();
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
                'jenis_laundry' => $validatedData['jenis_laundry'],
                'berat'         => $validatedData['berat'],
                'harga'         => $validatedData['harga'],
                'pembayaran'    => $validatedData['pembayaran'],
                'status'         => 'Belum Lunas'
            ]);

            OrderanDetail::create([
                'orderan_id' => $orderan->id,
                'nama'       => $validatedData['nama'],
                'no_hp'      => $validatedData['no_hp'],
                'alamat'     => $validatedData['alamat'],
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Orderan offline berhasil ditambahkan.');
        } catch (\Exception $e) {
            dd($e);
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
                'jenis_laundry' => $validatedData['jenis_laundry'],
                'berat'         => $validatedData['berat'],
                'harga'         => $validatedData['harga'],
                'pembayaran'    => $validatedData['pembayaran'],
                'berat'         => 'Belum Lunas'
            ]);

            OrderanDetail::where('orderan_id', $id)->update([
                'nama'       => $validatedData['nama'],
                'no_hp'      => $validatedData['no_hp'],
                'alamat'     => $validatedData['alamat'],
            ]);

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
            $orderan->update(['status' => 'Sudah Lunas']);

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
}
