<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\BiodataMahasiswa;
use Illuminate\Http\Request;

class BiodataController extends Controller
{
    public function show($nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        $biodata = BiodataMahasiswa::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->first();

        if (!$biodata) {
            return response()->json(['success' => false, 'message' => 'Biodata tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $biodata]);
    }

    public function store(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        $validated = $request->validate([
            'jenis_kelamin'        => 'nullable|in:Laki-Laki,Perempuan',
            'tempat_lahir'         => 'nullable|string|max:100',
            'tanggal_lahir'        => 'nullable|date',
            'agama'                => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya',
            'suku'                 => 'nullable|string|max:50',
            'berat_badan'          => 'nullable|integer',
            'tinggi_badan'         => 'nullable|integer',
            'golongan_darah'       => 'nullable|in:A,B,AB,O,Belum Tahu',
            'transportasi'         => 'nullable|in:Kendaraan Umum,Sepeda,Motor,Mobil',
            'no_telepon'           => 'nullable|string|max:20',
            'no_hp'                => 'required|string|max:20',
            'kepemilikan'          => 'nullable|in:Milik sendiri,Keluarga,Lainnya',
            'email_kampus'         => 'nullable|email|max:255',
            'email_pribadi'        => 'required|email|max:255',
            'id_negara'            => 'nullable|integer',
            'nik'                  => 'nullable|string|max:16',
            'paspor'               => 'nullable|string|max:50',
            'no_kk'                => 'nullable|string|max:16',
            'npwp'                 => 'nullable|string|max:25',
            'no_kps'               => 'nullable|string|max:50',
            'status_nikah'         => 'nullable|in:Lajang,Menikah',
            'ukuran_jas_almamater' => 'nullable|in:S,M,L,XL,XXL,XXXL',
            'file_akta_kelahiran'  => 'nullable|string|max:255',
        ]);

        $validated['id_mahasiswa'] = $mahasiswa->id_mahasiswa;

        $biodata = BiodataMahasiswa::create($validated);

        return response()->json(['success' => true, 'data' => $biodata], 201);
    }

    public function update(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $biodata = BiodataMahasiswa::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->first();

        if (!$biodata) {
            return response()->json(['success' => false, 'message' => 'Biodata tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'jenis_kelamin'        => 'sometimes|nullable|in:Laki-Laki,Perempuan',
            'tempat_lahir'         => 'sometimes|nullable|string|max:100',
            'tanggal_lahir'        => 'sometimes|nullable|date',
            'agama'                => 'sometimes|nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya',
            'suku'                 => 'sometimes|nullable|string|max:50',
            'berat_badan'          => 'sometimes|nullable|integer',
            'tinggi_badan'         => 'sometimes|nullable|integer',
            'golongan_darah'       => 'sometimes|nullable|in:A,B,AB,O,Belum Tahu',
            'transportasi'         => 'sometimes|nullable|in:Kendaraan Umum,Sepeda,Motor,Mobil',
            'no_telepon'           => 'sometimes|nullable|string|max:20',
            'no_hp'                => 'sometimes|string|max:20',
            'kepemilikan'          => 'sometimes|nullable|in:Milik sendiri,Keluarga,Lainnya',
            'email_kampus'         => 'sometimes|nullable|email|max:255',
            'email_pribadi'        => 'sometimes|email|max:255',
            'id_negara'            => 'sometimes|nullable|integer',
            'nik'                  => 'sometimes|nullable|string|max:16',
            'paspor'               => 'sometimes|nullable|string|max:50',
            'no_kk'                => 'sometimes|nullable|string|max:16',
            'npwp'                 => 'sometimes|nullable|string|max:25',
            'no_kps'               => 'sometimes|nullable|string|max:50',
            'status_nikah'         => 'sometimes|nullable|in:Lajang,Menikah',
            'ukuran_jas_almamater' => 'sometimes|nullable|in:S,M,L,XL,XXL,XXXL',
            'file_akta_kelahiran'  => 'sometimes|nullable|string|max:255',
        ]);

        $biodata->update($validated);

        return response()->json(['success' => true, 'data' => $biodata]);
    }

    public function destroy(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        $biodata = BiodataMahasiswa::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->first();

        if (!$biodata) {
            return response()->json(['success' => false, 'message' => 'Biodata tidak ditemukan'], 404);
        }

        $biodata->delete();

        return response()->json(['success' => true, 'message' => 'Biodata berhasil dihapus']);
    }
}