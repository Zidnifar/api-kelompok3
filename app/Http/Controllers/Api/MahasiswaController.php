<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Http\Resources\MahasiswaResource;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::with([
            'biodata', 'alamat', 'orangTuaWali', 'sekolah'
        ])->get();

        return response()->json([
            'success' => true,
            'data'    => MahasiswaResource::collection($mahasiswa)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim'                  => 'required|string|max:20|unique:mahasiswa,nim',
            'nama_mahasiswa'       => 'required|string|max:100',
            'tahunakademik_id'     => 'nullable|integer',
            'kurikulum_kode'       => 'nullable|string|max:12',
            'id_keuangan_mhs'      => 'Tidak Aktif|string|max:20',
            'id_dosen'             => 'nullable|string|max:36',
            'jurusan_id'           => 'nullable|integer',
            'prodi_id'             => 'nullable|integer',
            'jalur_pendaftaran'    => 'nullable|in:SNBP,SNBT,Mandiri,Prestasi,Kerja Sama,Lainnya',
            'gelombang'            => 'nullable|string|max:50',
            'tanggal_awal_masuk'   => 'nullable|date',
            'tanggal_daftar_ulang' => 'nullable|date',
            'is_kebutuhan_khusus'  => 'nullable|boolean',
        ]);

        $mahasiswa = Mahasiswa::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil ditambahkan',
            'data'    => new MahasiswaResource($mahasiswa)
        ], 201);
    }

    public function show(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if (
            $request->jwt_role === 'mahasiswa' &&
            $request->jwt_detail_id != $mahasiswa->id_mahasiswa
        ) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $mahasiswa->load(['biodata', 'alamat', 'orangTuaWali', 'sekolah']);

        return response()->json([
            'success' => true,
            'data'    => new MahasiswaResource($mahasiswa)
        ]);
    }

    public function update(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if (
            $request->jwt_role === 'mahasiswa' &&
            $request->jwt_detail_id != $mahasiswa->id_mahasiswa
        ) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'nama_mahasiswa'       => 'sometimes|string|max:100',
            'tahunakademik_id'     => 'sometimes|nullable|integer',
            'kurikulum_kode'       => 'sometimes|nullable|string|max:12',
            'id_keuangan_mhs'      => 'sometimes|nullable|string|max:20',
            'id_dosen'             => 'sometimes|nullable|string|max:36',
            'jurusan_id'           => 'sometimes|nullable|integer',
            'prodi_id'             => 'sometimes|nullable|integer',
            'jalur_pendaftaran'    => 'sometimes|nullable|in:SNBP,SNBT,Mandiri,Prestasi,Kerja Sama,Lainnya',
            'gelombang'            => 'sometimes|nullable|string|max:50',
            'tanggal_awal_masuk'   => 'sometimes|nullable|date',
            'tanggal_daftar_ulang' => 'sometimes|nullable|date',
            'is_kebutuhan_khusus'  => 'sometimes|nullable|boolean',
        ]);

        $mahasiswa->update($validated);
        $mahasiswa->load(['biodata', 'alamat', 'orangTuaWali', 'sekolah']);

        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil diperbarui',
            'data'    => new MahasiswaResource($mahasiswa)
        ]);
    }

    public function destroy($nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();
        $mahasiswa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil dihapus'
        ]);
    }
}