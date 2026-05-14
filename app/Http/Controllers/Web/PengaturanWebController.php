<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class PengaturanWebController extends Controller
{
    /** GET /pengaturan */
    public function index()
    {
        // Kirim semua settings ke view sebagai array
        $settings = [
            'nama_cafe'  => Setting::get('nama_cafe',  'siolivia'),
            'telepon'    => Setting::get('telepon',    ''),
            'alamat'     => Setting::get('alamat',     ''),
            'pajak'      => Setting::get('pajak',      '3'),
            'mata_uang'  => Setting::get('mata_uang',  'IDR'),
        ];

        return view('pengaturan.index', compact('settings'));
    }

    /** PUT /pengaturan */
    public function update(Request $request)
    {
        $request->validate([
            'nama_cafe' => 'required|string|max:100',
            'telepon'   => 'nullable|string|max:30',
            'alamat'    => 'nullable|string|max:255',
            'pajak'     => 'required|numeric|min:0|max:100',
            'mata_uang' => 'required|in:IDR,USD',
        ]);

        Setting::set('nama_cafe', $request->nama_cafe);
        Setting::set('telepon',   $request->telepon   ?? '');
        Setting::set('alamat',    $request->alamat     ?? '');
        Setting::set('pajak',     $request->pajak);
        Setting::set('mata_uang', $request->mata_uang);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}