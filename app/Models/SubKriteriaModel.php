<?php

namespace App\Models;

use CodeIgniter\Model;

class SubKriteriaModel extends Model
{
    protected $table            = 'sub_kriteria';
    protected $primaryKey       = 'id_sub_kriteria';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_kriteria', 'kode', 'nama', 'bobot_lokal', 'bobot_global'];

    public function getSubWithKriteria()
    {
        return $this->select('sub_kriteria.*, kriteria.nama as nama_kriteria')
                    ->join('kriteria', 'kriteria.id_kriteria = sub_kriteria.id_kriteria')
                    ->findAll();
    }
}