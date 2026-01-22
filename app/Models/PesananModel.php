<?php
namespace App\Models;
use CodeIgniter\Model;

class PesananModel extends Model {
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    protected $allowedFields = ['tanggal', 'id_jenis_bahan', 'jumlah_lusin', 'id_supplier', 'catatan'];

    public function getHistory() {
        return $this->select('pesanan.*, supplier.nama as nama_supplier, jenis_bahan.nama_bahan')
                    ->join('supplier', 'supplier.id_supplier = pesanan.id_supplier')
                    ->join('jenis_bahan', 'jenis_bahan.id_jenis_bahan = pesanan.id_jenis_bahan')
                    ->orderBy('tanggal', 'DESC')
                    ->findAll();
    }
}