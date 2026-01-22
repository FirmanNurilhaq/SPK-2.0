<?php
namespace App\Models;
use CodeIgniter\Model;

class NilaiSubBahanModel extends Model {
    protected $table = 'nilai_sub_bahan';
    protected $primaryKey = 'id_nilai_sub';
    protected $allowedFields = ['id_jenis_bahan', 'id_sub_kriteria', 'nilai_bobot_lokal', 'nilai_bobot_global'];
    
    public function getBobotByBahan($id_bahan) {
        return $this->where('id_jenis_bahan', $id_bahan)->findAll();
    }
}