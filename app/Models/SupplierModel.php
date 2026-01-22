<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table            = 'supplier';
    protected $primaryKey       = 'id_supplier';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['kode', 'nama', 'alamat', 'kontak', 'nilai_akhir'];
    
    protected $validationRules = [
        'kode' => 'required|is_unique[supplier.kode]',
        'nama' => 'required'
    ];
}