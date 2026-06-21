<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KrsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_krs'             => $this->id_krs,
            'id_mahasiswa'       => $this->id_mahasiswa,
            'nim'                => $this->mahasiswa?->nim,
            'nama_mahasiswa'     => $this->mahasiswa?->nama_mahasiswa,
            'prodi_id'           => $this->mahasiswa?->prodi_id,
            'tahunakademik_id'   => $this->tahunakademik_id,
            'semester_saat_ini'  => $this->semester_saat_ini,
            'batas_total_sks'    => $this->batas_total_sks,
            'status_krs'         => $this->status_krs,
            'catatan_pembimbing' => $this->catatan_pembimbing,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            
            // Relasi ke detail KRS
            'krs_detail' => KrsDetailResource::collection($this->whenLoaded('krsDetail')),
        ];
    }
}