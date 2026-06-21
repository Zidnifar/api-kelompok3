<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Krs extends Model
{
    protected $table = 'krs';
    protected $primaryKey = 'id_krs';

    protected $fillable = [
        'id_mahasiswa', 'tahunakademik_id', 'semester_saat_ini',
        'batas_total_sks', 'status_krs', 'catatan_pembimbing'
    ];

    public function detail()
    {
        return $this->hasMany(KrsDetail::class, 'id_krs', 'id_krs');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }
}