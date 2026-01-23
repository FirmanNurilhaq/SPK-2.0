<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananHistoryAhpModel extends Model
{
    protected $table            = 'pesanan_history_ahp';
    protected $primaryKey       = 'id_history';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_pesanan', 'id_supplier', 'skor_ahp', 'ranking'];

    // Helper untuk mengambil snapshot history beserta nama supplier
    // Dipakai saat menampilkan detail pesanan yang sudah selesai
    public function getSnapshot($id_pesanan)
    {
        return $this->select('pesanan_history_ahp.*, supplier.nama as nama_supplier')
                    ->join('supplier', 'supplier.id_supplier = pesanan_history_ahp.id_supplier')
                    ->where('id_pesanan', $id_pesanan)
                    ->orderBy('ranking', 'ASC')
                    ->findAll();
    }
}