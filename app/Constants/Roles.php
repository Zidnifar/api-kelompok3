<?php

namespace App\Constants;

class Roles
{
    // Semua role yang boleh MELIHAT data (GET) mahasiswa & turunannya
    const VIEW_MAHASISWA = 'admin-mahasiswa,admin-akademik,super-admin,admin-keuangan,admin-pegawai,dosen';

    // Role yang boleh CREATE/UPDATE/DELETE data inti mahasiswa, biodata, alamat, ortu, sekolah
    const MANAGE_MAHASISWA = 'admin-mahasiswa,super-admin';

    // Role yang boleh hapus KRS (lebih luas dari MANAGE_MAHASISWA)
    const MANAGE_KRS_DELETE = 'admin-mahasiswa,super-admin,dosen';
}