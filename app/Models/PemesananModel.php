<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananModel extends Model
{
    protected $table            = 'pesanan';
    protected $primaryKey       = 'id_pesanan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['tanggal', 'jumlah_lusin', 'bahan_baku', 'id_supplier_terpilih', 'catatan'];

    public function getHistoryLengkap($id = null)
    {
        $builder = $this->select('pesanan.*, supplier.nama as nama_supplier')
                        ->join('supplier', 'supplier.id_supplier = pesanan.id_supplier_terpilih', 'left');
        
        if ($id) {
            return $builder->where('id_pesanan', $id)->first();
        }
        
        return $builder->orderBy('tanggal', 'DESC')->findAll();
    }
}