<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisBahanModel extends Model
{
    protected $table            = 'jenis_bahan';
    protected $primaryKey       = 'id_jenis_bahan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // PERUBAHAN DISINI: Tambahkan 'harga' agar bisa di-input
    protected $allowedFields    = ['nama_bahan', 'harga', 'keterangan'];
}