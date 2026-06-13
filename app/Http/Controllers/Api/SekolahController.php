<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\SekolahMahasiswa;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
    public function show(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $sekolah = SekolahMahasiswa::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->first();

        if (!$sekolah) {
            return response()->json(['success' => false, 'message' => 'Data sekolah tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $sekolah]);
    }

    public function store(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        $validated = $request->validate([
            'pendidikan_asal'      => 'required|in:MA,SMK,SMA,Paket C,Pondok Pesantren,SMA Di Luar Negeri',
            'id_provinsi_sekolah'  => 'nullable|integer',
            'id_kota_sekolah'      => 'nullable|integer',
            'nama_sekolah'         => 'required|string|max:150',
            'alamat_sekolah'       => 'nullable|string',
            'telepon_sekolah'      => 'nullable|string|max:20',
            'nomor_ijazah'         => 'nullable|string|max:50',
            'nisn'                 => 'required|string|max:20',
            'file_ijazah_terakhir' => 'nullable|string|max:255',
        ]);

        $validated['id_mahasiswa'] = $mahasiswa->id_mahasiswa;

        $sekolah = SekolahMahasiswa::create($validated);

        return response()->json(['success' => true, 'data' => $sekolah], 201);
    }

    public function update(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $sekolah = SekolahMahasiswa::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->first();

        if (!$sekolah) {
            return response()->json(['success' => false, 'message' => 'Data sekolah tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'pendidikan_asal'      => 'sometimes|in:MA,SMA,SMK,Paket C,Pondok Pesantren,SMA Di Luar Negeri',
            'id_provinsi_sekolah'  => 'sometimes|nullable|integer',
            'id_kota_sekolah'      => 'sometimes|nullable|integer',
            'nama_sekolah'         => 'sometimes|string|max:150',
            'alamat_sekolah'       => 'sometimes|nullable|string',
            'telepon_sekolah'      => 'sometimes|nullable|string|max:20',
            'nomor_ijazah'         => 'sometimes|nullable|string|max:50',
            'nisn'                 => 'sometimes|string|max:20',
            'file_ijazah_terakhir' => 'sometimes|nullable|string|max:255',
        ]);

        $sekolah->update($validated);

        return response()->json(['success' => true, 'data' => $sekolah]);
    }

    public function destroy(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        $sekolah = SekolahMahasiswa::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->first();

        if (!$sekolah) {
            return response()->json(['success' => false, 'message' => 'Data sekolah tidak ditemukan'], 404);
        }

        $sekolah->delete();

        return response()->json(['success' => true, 'message' => 'Data sekolah berhasil dihapus']);
    }
}