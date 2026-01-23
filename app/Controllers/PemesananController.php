<?php
namespace App\Controllers;
use App\Models\PesananModel;
use App\Models\JenisBahanModel;
use App\Models\PembeliModel;

class PemesananController extends BaseController {
    protected $pesananModel;
    protected $jenisBahanModel;
    protected $pembeliModel;

    public function __construct() {
        $this->pesananModel = new PesananModel();
        $this->jenisBahanModel = new JenisBahanModel();
        $this->pembeliModel = new PembeliModel();
    }

    public function index() {
        $data = ['history' => $this->pesananModel->getPesananLengkap()];
        return view('pemesanan/dashboard', $data);
    }

    public function pembeli() {
        return view('pemesanan/pembeli', ['pembeli' => $this->pembeliModel->findAll()]);
    }

    public function storePembeli() {
        $this->pembeliModel->save([
            'nama_pembeli' => $this->request->getPost('nama'),
            'alamat' => $this->request->getPost('alamat'),
            'kontak' => $this->request->getPost('kontak')
        ]);
        return redirect()->to('/pemesanan/pembeli')->with('success', 'Pembeli ditambahkan');
    }

    public function create() {
        return view('pemesanan/create', [
            'bahan' => $this->jenisBahanModel->findAll(),
            'pembeli' => $this->pembeliModel->findAll()
        ]);
    }

    public function store() {
        $lusin = $this->request->getPost('jumlah_lusin');
        $id_bahan = $this->request->getPost('id_jenis_bahan');
        
        // 1. Ambil Harga Satuan dari Database (Biar aman)
        $bahan = $this->jenisBahanModel->find($id_bahan);
        $harga_satuan = $bahan['harga'];

        // 2. Hitung Total
        $berat = $lusin * 2.7; 
        $total_harga = $lusin * $harga_satuan; // Rumus: Lusin x Harga

        $this->pesananModel->save([
            'id_pembeli' => $this->request->getPost('id_pembeli'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'id_jenis_bahan' => $id_bahan,
            'jumlah_lusin' => $lusin,
            'total_berat' => $berat,
            'total_harga' => $total_harga, // <--- Simpan hasil hitungan
            'catatan' => $this->request->getPost('catatan'),
            'status' => 'pending',
            'tanggal' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/pemesanan')->with('success', 'Pesanan berhasil dibuat! Total: Rp ' . number_format($total_harga));
    }
}