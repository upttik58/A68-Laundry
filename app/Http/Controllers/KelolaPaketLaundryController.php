<?php

namespace App\Http\Controllers;

use App\Models\JenisLaundry;
use App\Models\PaketLaundry;
use Illuminate\Http\Request;

class KelolaPaketLaundryController extends Controller
{
    public function index()
    {
        $paketLaundry = PaketLaundry::with('jenisLaundry')->get();
        $jenisLaundry = JenisLaundry::all();
        return view('owners.paket_laundry.paket_laundry', compact('paketLaundry', 'jenisLaundry'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_laundry_id' => 'required',
            'harga' => 'required',
            'berat' => 'required'
        ]);

        try {
            PaketLaundry::create([
                'kode_paket' => substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8) . '_' . time(),
                'jenis_laundry_id' => $validated['jenis_laundry_id'],
                'harga' => $validated['harga'],
                'berat' => $validated['berat']
            ]);
            return redirect('/paketLaundry')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect('/paketLaundry')->with('error', 'Data gagal ditambahkan: ' . $e->getMessage());
        }
    }

    public function destroy($id){
        try {
            $jenisLaundry = PaketLaundry::findOrFail($id);
            $jenisLaundry->delete();
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
        $validated = $request->validate([
            'jenis_laundry_id' => 'required',
            'harga' => 'required',
            'berat' => 'required'
        ]);

        try {
            PaketLaundry::where('id', $id)->update($validated);
            return redirect('/paketLaundry')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            return redirect('/paketLaundry')->with('error', 'Data gagal diubah: ' . $e->getMessage());
        }
    }
}
