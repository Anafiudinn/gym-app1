<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    // 🔹 tampil data
    public function index()
    {
        $paket = Paket::latest()->get();
        return view('paket.index', compact('paket'));
    }

    // 🔹 simpan paket
    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required',
            'harga' => 'required|numeric',
            'durasi_hari' => 'required|numeric'
        ]);

        Paket::create($request->all());

        return back()->with('success', 'Paket berhasil ditambahkan');
    }

    // 🔹 update paket
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_paket' => 'required',
            'harga' => 'required|numeric',
            'durasi_hari' => 'required|numeric'
        ]);

        $paket = Paket::findOrFail($id);
        $paket->update($request->all());

        return back()->with('success', 'Paket berhasil diupdate');
    }

    // 🔹 hapus paket
    public function destroy($id)
    {
        Paket::destroy($id);
        return back()->with('success', 'Paket berhasil dihapus');
    }
}