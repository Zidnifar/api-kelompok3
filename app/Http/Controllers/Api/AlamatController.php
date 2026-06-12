<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\AlamatMahasiswa;
use Illuminate\Http\Request;

class AlamatController extends Controller
{
    public function show(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $alamat = AlamatMahasiswa::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->first();

        if (!$alamat) {
            return response()->json(['success' => false, 'message' => 'Alamat tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $alamat]);
    }

    public function store(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        $validated = $request->validate([
            // KTP
            'alamat_ktp'             => 'required|string',
            'rt_ktp'                 => 'nullable|string|max:5',
            'rw_ktp'                 => 'nullable|string|max:5',
            'dusun_ktp'              => 'nullable|string|max:100',
            'id_kelurahan_ktp'       => 'nullable|integer',
            'id_kecamatan_ktp'       => 'nullable|integer',
            'id_kota_ktp'            => 'nullable|integer',
            'id_provinsi_ktp'        => 'nullable|integer',
            'kode_pos_ktp'           => 'required|string|max:10',
            'is_domisili_sesuai_ktp' => 'nullable|boolean',
            // Domisili
            'alamat_domisili'        => 'nullable|string',
            'rt_domisili'            => 'nullable|string|max:5',
            'rw_domisili'            => 'nullable|string|max:5',
            'dusun_domisili'         => 'nullable|string|max:100',
            'id_kelurahan_domisili'  => 'nullable|integer',
            'id_kecamatan_domisili'  => 'nullable|integer',
            'id_kota_domisili'       => 'nullable|integer',
            'id_provinsi_domisili'   => 'nullable|integer',
            'kode_pos_domisili'      => 'nullable|string|max:10',
            'status_tinggal'         => 'nullable|in:Orang Tua,Kos,Asrama,Kontrak,Lainnya',
        ]);

        $validated['id_mahasiswa'] = $mahasiswa->id_mahasiswa;

        $alamat = AlamatMahasiswa::create($validated);

        return response()->json(['success' => true, 'data' => $alamat], 201);
    }

    public function update(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $alamat = AlamatMahasiswa::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->first();

        if (!$alamat) {
            return response()->json(['success' => false, 'message' => 'Alamat tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            // KTP
            'alamat_ktp'             => 'sometimes|string',
            'rt_ktp'                 => 'sometimes|nullable|string|max:5',
            'rw_ktp'                 => 'sometimes|nullable|string|max:5',
            'dusun_ktp'              => 'sometimes|nullable|string|max:100',
            'id_kelurahan_ktp'       => 'sometimes|nullable|integer',
            'id_kecamatan_ktp'       => 'sometimes|nullable|integer',
            'id_kota_ktp'            => 'sometimes|nullable|integer',
            'id_provinsi_ktp'        => 'sometimes|nullable|integer',
            'kode_pos_ktp'           => 'sometimes|string|max:10',
            'is_domisili_sesuai_ktp' => 'sometimes|nullable|boolean',
            // Domisili
            'alamat_domisili'        => 'sometimes|nullable|string',
            'rt_domisili'            => 'sometimes|nullable|string|max:5',
            'rw_domisili'            => 'sometimes|nullable|string|max:5',
            'dusun_domisili'         => 'sometimes|nullable|string|max:100',
            'id_kelurahan_domisili'  => 'sometimes|nullable|integer',
            'id_kecamatan_domisili'  => 'sometimes|nullable|integer',
            'id_kota_domisili'       => 'sometimes|nullable|integer',
            'id_provinsi_domisili'   => 'sometimes|nullable|integer',
            'kode_pos_domisili'      => 'sometimes|nullable|string|max:10',
            'status_tinggal'         => 'sometimes|nullable|in:Orang Tua,Kos,Asrama,Kontrak,Lainnya',
        ]);

        $alamat->update($validated);

        return response()->json(['success' => true, 'data' => $alamat]);
    }

    public function destroy(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        $alamat = AlamatMahasiswa::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->first();

        if (!$alamat) {
            return response()->json(['success' => false, 'message' => 'Alamat tidak ditemukan'], 404);
        }

        $alamat->delete();

        return response()->json(['success' => true, 'message' => 'Alamat berhasil dihapus']);
    }
}