<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\OrangTuaWali;
use Illuminate\Http\Request;

class OrangTuaWaliController extends Controller
{
    public function index(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $ortu = OrangTuaWali::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->get();

        return response()->json(['success' => true, 'data' => $ortu]);
    }

    public function store(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        $validated = $request->validate([
            'jenis_peran'         => 'required|in:Ayah,Ibu,Wali',
            'nama_lengkap'        => 'required|string|max:100',
            'nik'                 => 'nullable|string|max:16',
            'tanggal_lahir'       => 'nullable|date',
            'status_hidup'        => 'nullable|in:Hidup,Meninggal,Tidak Diketahui',
            'status_kekerabatan'  => 'nullable|in:Kandung,Tiri,Angkat,Lainnya',
            'pendidikan_terakhir' => 'nullable|in:SD - Sekolah Dasar,SMP - SMP/Sederajat,SMA - SMA/SMK Sederajat,D1 - Diploma 1,D2 - Diploma 2,D3 - Diploma 3,D4 - Sarjana Terapan,S1 - Strata 1,Prof - Profesi,S2 - Strata 2,MTr - S2 Terapan,Sp-1 - Spesialis 1,S3 - Strata 3,DTr - S3 Terapan,Sp-2 - Spesialis 2',
            'pekerjaan'           => 'nullable|in:Tidak Bekerja,Bekerja,Ibu Rumah Tangga,Belum Bekerja,PNS',
            'penghasilan'         => 'nullable|in:Kurang dari/Sama dengan 500.000,500.000 - 2.000.000,2.000.000 - 5.000.000,Diatas 5.000.000',
            'alamat'              => 'nullable|string',
            'no_telepon'          => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:255',
        ]);

        $validated['id_mahasiswa'] = $mahasiswa->id_mahasiswa;

        $ortu = OrangTuaWali::create($validated);

        return response()->json(['success' => true, 'data' => $ortu], 201);
    }

    public function update(Request $request, $nim, $id_ortu)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $ortu = OrangTuaWali::where('id_mahasiswa', $mahasiswa->id_mahasiswa)
            ->where('id_ortu_wali', $id_ortu)
            ->first();

        if (!$ortu) {
            return response()->json(['success' => false, 'message' => 'Data orang tua/wali tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'jenis_peran'         => 'sometimes|in:Ayah,Ibu,Wali',
            'nama_lengkap'        => 'sometimes|string|max:100',
            'nik'                 => 'sometimes|nullable|string|max:16',
            'tanggal_lahir'       => 'sometimes|nullable|date',
            'status_hidup'        => 'sometimes|nullable|in:Hidup,Meninggal,Tidak Diketahui',
            'status_kekerabatan'  => 'sometimes|nullable|in:Kandung,Tiri,Angkat,Lainnya',
            'pendidikan_terakhir' => 'sometimes|nullable|in:SD - Sekolah Dasar,SMP - SMP/Sederajat,SMA - SMA/SMK Sederajat,D1 - Diploma 1,D2 - Diploma 2,D3 - Diploma 3,D4 - Sarjana Terapan,S1 - Strata 1,Prof - Profesi,S2 - Strata 2,MTr - S2 Terapan,Sp-1 - Spesialis 1,S3 - Strata 3,DTr - S3 Terapan,Sp-2 - Spesialis 2',
            'pekerjaan'           => 'sometimes|nullable|in:Tidak Bekerja,Bekerja,Ibu Rumah Tangga,Belum Bekerja,PNS',
            'penghasilan'         => 'sometimes|nullable|in:Kurang dari/Sama dengan 500.000,500.000 - 2.000.000,2.000.000 - 5.000.000,Diatas 5.000.000',
            'alamat'              => 'sometimes|nullable|string',
            'no_telepon'          => 'sometimes|nullable|string|max:20',
            'email'               => 'sometimes|nullable|email|max:255',
        ]);

        $ortu->update($validated);

        return response()->json(['success' => true, 'data' => $ortu]);
    }

    public function destroy(Request $request, $nim, $id_ortu)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        $ortu = OrangTuaWali::where('id_mahasiswa', $mahasiswa->id_mahasiswa)
            ->where('id_ortu_wali', $id_ortu)
            ->first();

        if (!$ortu) {
            return response()->json(['success' => false, 'message' => 'Data orang tua/wali tidak ditemukan'], 404);
        }

        $ortu->delete();

        return response()->json(['success' => true, 'message' => 'Data orang tua/wali berhasil dihapus']);
    }
}