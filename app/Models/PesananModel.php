<?php
namespace App\Models;
use CodeIgniter\Model;

class PesananModel extends Model {
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    // Tambahkan 'total_harga' di sini
    protected $allowedFields = ['tanggal', 'id_jenis_bahan', 'id_pembeli', 'nama_barang', 'jumlah_lusin', 'total_berat', 'total_harga', 'id_supplier', 'catatan', 'status'];

    public function getPesananLengkap($status = null) {
        // ... (Kode sama seperti sebelumnya) ...
        $builder = $this->select('pesanan.*, jenis_bahan.nama_bahan, pembeli.nama_pembeli, supplier.nama as nama_supplier')
                        ->join('jenis_bahan', 'jenis_bahan.id_jenis_bahan = pesanan.id_jenis_bahan')
                        ->join('pembeli', 'pembeli.id_pembeli = pesanan.id_pembeli')
                        ->join('supplier', 'supplier.id_supplier = pesanan.id_supplier', 'left'); 
        
        if($status) {
            $builder->where('pesanan.status', $status);
        }
        
        return $builder->orderBy('tanggal', 'DESC')->findAll();
    }
}