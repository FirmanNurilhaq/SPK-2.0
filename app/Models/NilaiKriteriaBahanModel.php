<?php
namespace App\Models;
use CodeIgniter\Model;

class NilaiKriteriaBahanModel extends Model {
    protected $table = 'nilai_kriteria_bahan';
    protected $primaryKey = 'id_nilai_kriteria';
    protected $allowedFields = ['id_jenis_bahan', 'id_kriteria', 'nilai_bobot'];

    // Ambil semua bobot kriteria untuk bahan tertentu
    public function getBobotByBahan($id_bahan) {
        return $this->where('id_jenis_bahan', $id_bahan)->findAll();
    }
}