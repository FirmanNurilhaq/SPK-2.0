<?php
namespace App\Models;
use CodeIgniter\Model;

class NilaiSupplierBahanModel extends Model {
    protected $table = 'nilai_supplier_bahan';
    protected $primaryKey = 'id_nilai_supplier';
    protected $allowedFields = ['id_jenis_bahan', 'id_supplier', 'id_sub_kriteria', 'nilai_skor'];

    // Ambil skor supplier tertentu untuk kalkulasi leaderboard
    public function getSkorByBahan($id_bahan) {
        return $this->where('id_jenis_bahan', $id_bahan)->findAll();
    }
}