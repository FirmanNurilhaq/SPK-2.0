<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\JenisBahanModel;
use App\Models\SupplierModel;
use App\Models\NilaiSubBahanModel;
use App\Models\NilaiSupplierBahanModel;

class PemesananController extends BaseController
{
    protected $pesananModel;
    protected $jenisBahanModel;
    protected $supplierModel;
    protected $nilaiSubBahanModel;
    protected $nilaiSupplierBahanModel;
    protected $db;

    public function __construct()
    {
        $this->pesananModel = new PesananModel();
        $this->jenisBahanModel = new JenisBahanModel();
        $this->supplierModel = new SupplierModel();
        $this->nilaiSubBahanModel = new NilaiSubBahanModel();
        $this->nilaiSupplierBahanModel = new NilaiSupplierBahanModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = ['history' => $this->pesananModel->getHistory()];
        return view('pemesanan/index', $data);
    }

    public function create()
    {
        $data = ['bahan_list' => $this->jenisBahanModel->findAll()];
        return view('pemesanan/create', $data);
    }

    // API: Dipanggil via AJAX saat user pilih bahan di form
    public function getLeaderboard($id_bahan)
    {
        $suppliers = $this->supplierModel->findAll();
        $leaderboard = [];

        // Ambil semua sub kriteria yang punya bobot global di bahan ini
        $subsBobot = $this->nilaiSubBahanModel->getBobotByBahan($id_bahan);
        
        // Mapping Bobot Global Sub: [id_sub => 0.xxx]
        $mapBobotSub = [];
        foreach($subsBobot as $sb) {
            $mapBobotSub[$sb['id_sub_kriteria']] = $sb['nilai_bobot_global'];
        }

        foreach ($suppliers as $s) {
            $totalSkor = 0;
            // Ambil skor supplier ini untuk bahan ini
            $skorData = $this->nilaiSupplierBahanModel
                ->where('id_jenis_bahan', $id_bahan)
                ->where('id_supplier', $s['id_supplier'])
                ->findAll();

            // Hitung SAW (Simple Additive Weighting) dari hasil AHP
            foreach($skorData as $sd) {
                if(isset($mapBobotSub[$sd['id_sub_kriteria']])) {
                    $globalWeight = $mapBobotSub[$sd['id_sub_kriteria']];
                    $totalSkor += ($globalWeight * $sd['nilai_skor']);
                }
            }

            $leaderboard[] = [
                'id_supplier' => $s['id_supplier'],
                'nama' => $s['nama'],
                'skor_akhir' => $totalSkor
            ];
        }

        // Urutkan Descending
        usort($leaderboard, function ($a, $b) {
            return $b['skor_akhir'] <=> $a['skor_akhir'];
        });

        return $this->response->setJSON($leaderboard);
    }

    public function store()
    {
        $id_bahan = $this->request->getPost('id_jenis_bahan');
        
        // Simpan Pesanan
        $this->pesananModel->insert([
            'id_jenis_bahan' => $id_bahan,
            'jumlah_lusin' => $this->request->getPost('jumlah_lusin'),
            'id_supplier' => $this->request->getPost('id_supplier'),
            'catatan' => $this->request->getPost('catatan'),
            'tanggal' => date('Y-m-d H:i:s')
        ]);
        $id_pesanan = $this->pesananModel->getInsertID();

        // Simpan Snapshot Leaderboard (Panggil fungsi hitung manual)
        // Kita hitung ulang di sini untuk disimpan ke history
        $leaderboardJSON = $this->getLeaderboard($id_bahan)->getBody();
        $leaderboard = json_decode($leaderboardJSON, true);

        foreach($leaderboard as $rank => $row) {
            $this->db->table('pesanan_history_ahp')->insert([
                'id_pesanan' => $id_pesanan,
                'id_supplier' => $row['id_supplier'],
                'skor_ahp' => $row['skor_akhir'],
                'ranking' => $rank + 1
            ]);
        }

        return redirect()->to('/pemesanan')->with('success', 'Pesanan berhasil dibuat!');
    }

    public function detail($id)
    {
        $pesananModel = new \App\Models\PesananModel(); // Load ulang agar fungsi getHistory jalan
        // Custom query karena getHistory() return array all
        $pesanan = $this->db->table('pesanan')
            ->select('pesanan.*, supplier.nama as nama_supplier, jenis_bahan.nama_bahan')
            ->join('supplier', 'supplier.id_supplier = pesanan.id_supplier')
            ->join('jenis_bahan', 'jenis_bahan.id_jenis_bahan = pesanan.id_jenis_bahan')
            ->where('id_pesanan', $id)
            ->get()->getRowArray();
            
        $historyModel = new \App\Models\PesananHistoryAhpModel();
        $history = $historyModel->getSnapshot($id);

        return view('pemesanan/detail', ['pesanan' => $pesanan, 'history' => $history]);
    }
}