<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Http\Resources\KrsResource;
use Illuminate\Http\Request;

class KrsController extends Controller
{
    public function index(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $krs = Krs::where('id_mahasiswa', $mahasiswa->id_mahasiswa)
            ->with('detail')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $krs
        ]);
    }

    // =========================================================
    // GET /mahasiswa/{nim}/krs-detail
    // Endpoint terpisah: KRS + info nim, nama_mahasiswa, prodi_id
    // =========================================================
    public function indexWithMahasiswa(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $krs = Krs::where('id_mahasiswa', $mahasiswa->id_mahasiswa)
            ->with(['detail', 'mahasiswa'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => KrsResource::collection($krs)
        ]);
    }

    public function store(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'tahunakademik_id'   => 'nullable|integer',
            'semester_saat_ini'  => 'required|integer|min:1|max:14',
            'batas_total_sks'    => 'required|integer|min:1|max:24',
            'status_krs'         => 'nullable|in:Draft,Diajukan,Divalidasi,Ditolak',
            'catatan_pembimbing' => 'nullable|string',
        ]);

        $validated['id_mahasiswa'] = $mahasiswa->id_mahasiswa;

        $krs = Krs::create($validated);

        return response()->json(['success' => true, 'data' => $krs], 201);
    }

    public function show(Request $request, $nim, $id_krs)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $krs = Krs::where('id_mahasiswa', $mahasiswa->id_mahasiswa)
            ->where('id_krs', $id_krs)
            ->with('detail')
            ->first();

        if (!$krs) {
            return response()->json(['success' => false, 'message' => 'Data KRS tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $krs]);
    }

    public function update(Request $request, $nim, $id_krs)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $krs = Krs::where('id_mahasiswa', $mahasiswa->id_mahasiswa)
            ->where('id_krs', $id_krs)
            ->first();

        if (!$krs) {
            return response()->json(['success' => false, 'message' => 'Data KRS tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'tahunakademik_id'   => 'sometimes|nullable|integer',
            'semester_saat_ini'  => 'sometimes|integer|min:1|max:14',
            'batas_total_sks'    => 'sometimes|integer|min:1|max:24',
            'status_krs'         => 'sometimes|nullable|in:Draft,Diajukan,Divalidasi,Ditolak',
            'catatan_pembimbing' => 'sometimes|nullable|string',
        ]);

        $krs->update($validated);

        return response()->json(['success' => true, 'data' => $krs]);
    }

    public function destroy(Request $request, $nim, $id_krs)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        $krs = Krs::where('id_mahasiswa', $mahasiswa->id_mahasiswa)
            ->where('id_krs', $id_krs)
            ->first();

        if (!$krs) {
            return response()->json(['success' => false, 'message' => 'Data KRS tidak ditemukan'], 404);
        }

        $krs->delete();

        return response()->json(['success' => true, 'message' => 'KRS berhasil dihapus']);
    }

    public function detailIndex(Request $request, $nim, $id_krs)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $detail = KrsDetail::where('id_krs', $id_krs)->get();

        return response()->json(['success' => true, 'data' => $detail]);
    }

    public function detailStore(Request $request, $nim, $id_krs)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'mk_kode' => 'required|string|max:12',
        ]);

        $validated['id_krs'] = $id_krs;

        $detail = KrsDetail::create($validated);

        return response()->json(['success' => true, 'data' => $detail], 201);
    }

    public function detailDestroy(Request $request, $nim, $id_krs, $id_detail)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        if ($request->jwt_role === 'mahasiswa' && $request->jwt_detail_id != $mahasiswa->id_mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $detail = KrsDetail::where('id_krs', $id_krs)
            ->where('id_krs_detail', $id_detail)
            ->first();

        if (!$detail) {
            return response()->json(['success' => false, 'message' => 'Detail KRS tidak ditemukan'], 404);
        }

        $detail->delete();

        return response()->json(['success' => true, 'message' => 'MK berhasil dihapus dari KRS']);
    }
}