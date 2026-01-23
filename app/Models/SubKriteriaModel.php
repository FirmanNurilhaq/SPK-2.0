<?php
namespace App\Models;
use CodeIgniter\Model;

class SubKriteriaModel extends Model {
    protected $table = 'sub_kriteria';
    protected $primaryKey = 'id_sub_kriteria';
    // TAMBAHKAN 'bobot_lokal' DAN 'bobot_global' DISINI:
    protected $allowedFields = ['id_kriteria', 'kode', 'nama', 'bobot_lokal', 'bobot_global'];

    // Helper untuk ambil sub beserta nama parent-nya
    public function getSubLengkap() {
        return $this->select('sub_kriteria.*, kriteria.nama as nama_kriteria')
                    ->join('kriteria', 'kriteria.id_kriteria = sub_kriteria.id_kriteria')
                    ->findAll();
    }
}