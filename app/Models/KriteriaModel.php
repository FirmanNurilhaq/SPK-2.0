<?php

namespace App\Models;

use CodeIgniter\Model;

class KriteriaModel extends Model
{
    protected $table            = 'kriteria';
    protected $primaryKey       = 'id_kriteria';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['kode', 'nama', 'bobot_global'];

    // Validasi sederhana
    protected $validationRules = [
        'kode' => 'required|is_unique[kriteria.kode]',
        'nama' => 'required'
    ];
}