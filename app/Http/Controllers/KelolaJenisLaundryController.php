<?php

namespace App\Http\Controllers;

use App\Models\JenisLaundry;
use Illuminate\Http\Request;

class KelolaJenisLaundryController extends Controller
{
    public function index(){
        $jenisLaundry = JenisLaundry::all();
        return view('owners.jenis_laundry.jenis_laundry', compact('jenisLaundry'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            'nama' => 'required',
            'harga' => 'required',
            'waktu' => 'required',
            'berat' => 'required'
        ]);

        try {
            JenisLaundry::create($validated);
            return redirect('/jenisLaundry')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect('/jenisLaundry')->with('error', 'Data gagal ditambahkan: ' . $e->getMessage());
        }
    }

    public function destroy($id){
        try {
            $jenisLaundry = JenisLaundry::findOrFail($id);
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

    public function update(Request $request, $id){
        $validated = $request->validate([
            'nama' => 'required',
            'harga' => 'required',
            'waktu' => 'required',
            'berat' => 'required'
        ]);

        try {
            JenisLaundry::where('id', $id)->update($validated);
            return redirect('/jenisLaundry')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            return redirect('/jenisLaundry')->with('error', 'Data gagal diubah: ' . $e->getMessage());
        }
    }
}
