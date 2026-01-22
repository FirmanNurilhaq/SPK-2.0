<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisBahanModel extends Model  // <--- Perhatikan bagian ini
{
    protected $table            = 'jenis_bahan';
    protected $primaryKey       = 'id_jenis_bahan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_bahan', 'keterangan'];
}