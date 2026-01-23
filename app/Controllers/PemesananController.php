<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\JenisBahanModel;
use App\Models\SupplierModel;
use App\Models\SubKriteriaModel; // Pakai ini untuk bobot global
use App\Models\NilaiSupplierBahanModel; // Pakai ini untuk skor supplier dinamis
use App\Models\PesananHistoryAhpModel;

class PemesananController extends BaseController
{
    protected $pesananModel;
    protected $jenisBahanModel;
    protected $supplierModel;
    protected $subKriteriaModel;
    protected $nilaiSupplierBahanModel;
    protected $pesananHistoryAhpModel;
    protected $db;

    public function __construct()
    {
        $this->pesananModel = new PesananModel();
        $this->jenisBahanModel = new JenisBahanModel();
        $this->supplierModel = new SupplierModel();
        $this->subKriteriaModel = new SubKriteriaModel();
        $this->nilaiSupplierBahanModel = new NilaiSupplierBahanModel();
        $this->pesananHistoryAhpModel = new PesananHistoryAhpModel();
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

    // --- LOGIC INTI PERHITUNGAN AHP (SAW) ---
    // Dipisahkan jadi fungsi private agar bisa dipanggil oleh AJAX maupun saat Save Pesanan
    private function hitungRekomendasi($id_bahan)
    {
        $suppliers = $this->supplierModel->findAll();
        $leaderboard = [];

        // 1. Ambil Bobot Global Sub Kriteria dari MASTER (Static)
        // Pastikan kolom 'bobot_global' ada di tabel sub_kriteria
        $allSubs = $this->subKriteriaModel->findAll();
        
        // 2. Ambil Skor Supplier untuk Bahan ini (Dynamic)
        $skorData = $this->nilaiSupplierBahanModel
            ->where('id_jenis_bahan', $id_bahan)
            ->findAll();

        // Mapping Skor Supplier biar mudah diakses: [id_supplier][id_sub] => nilai
        $mapSkor = [];
        foreach($skorData as $sd) {
            $mapSkor[$sd['id_supplier']][$sd['id_sub_kriteria']] = $sd['nilai_skor'];
        }

        // 3. Hitung Total SAW (Simple Additive Weighting)
        foreach ($suppliers as $s) {
            $totalSkor = 0;
            $id_sup = $s['id_supplier'];

            foreach($allSubs as $sub) {
                // Rumus: Bobot Global Sub (Master) * Skor Supplier (Lokal Bahan)
                $bobotGlobal = $sub['bobot_global']; 
                
                // Jika supplier belum dinilai di sub ini, anggap nilai 0 (atau bisa dihandle lain)
                $skor = isset($mapSkor[$id_sup][$sub['id_sub_kriteria']]) ? $mapSkor[$id_sup][$sub['id_sub_kriteria']] : 0;
                
                $totalSkor += ($bobotGlobal * $skor);
            }

            $leaderboard[] = [
                'id_supplier' => $id_sup,
                'nama' => $s['nama'],
                'skor_akhir' => $totalSkor
            ];
        }

        // Urutkan dari skor tertinggi ke terendah
        usort($leaderboard, function ($a, $b) {
            return $b['skor_akhir'] <=> $a['skor_akhir'];
        });

        return $leaderboard;
    }

    // API: Dipanggil via AJAX saat user pilih bahan di dropdown
    public function getLeaderboard($id_bahan)
    {
        try {
            $leaderboard = $this->hitungRekomendasi($id_bahan);
            return $this->response->setJSON($leaderboard);
        } catch (\Exception $e) {
            // Tangkap error biar ketahuan di console network tab
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function store()
    {
        $id_bahan = $this->request->getPost('id_jenis_bahan');
        
        // 1. Simpan Data Pesanan Utama
        $this->pesananModel->save([
            'id_jenis_bahan' => $id_bahan,
            'jumlah_lusin' => $this->request->getPost('jumlah_lusin'),
            'id_supplier' => $this->request->getPost('id_supplier'), // Supplier yang dipilih user (bisa jadi bukan ranking 1)
            'catatan' => $this->request->getPost('catatan'),
            'tanggal' => date('Y-m-d H:i:s')
        ]);
        
        $id_pesanan = $this->pesananModel->getInsertID();

        // 2. Simpan Snapshot Leaderboard (History AHP)
        // Kita hitung ulang saat itu juga untuk disimpan sebagai bukti sejarah
        $leaderboard = $this->hitungRekomendasi($id_bahan);

        foreach($leaderboard as $rank => $row) {
            // Index array mulai dari 0, jadi ranking = index + 1
            $ranking = $rank + 1;
            
            $this->pesananHistoryAhpModel->save([
                'id_pesanan' => $id_pesanan,
                'id_supplier' => $row['id_supplier'],
                'skor_ahp' => $row['skor_akhir'],
                'ranking' => $ranking
            ]);
        }

        return redirect()->to('/pemesanan')->with('success', 'Pesanan berhasil dibuat!');
    }

    public function detail($id)
    {
        // Custom query karena kita butuh join lengkap
        $pesanan = $this->db->table('pesanan')
            ->select('pesanan.*, supplier.nama as nama_supplier, jenis_bahan.nama_bahan')
            ->join('supplier', 'supplier.id_supplier = pesanan.id_supplier')
            ->join('jenis_bahan', 'jenis_bahan.id_jenis_bahan = pesanan.id_jenis_bahan')
            ->where('id_pesanan', $id)
            ->get()->getRowArray();
            
        // Ambil history ranking pada saat pesanan itu dibuat
        $history = $this->pesananHistoryAhpModel->getSnapshot($id);

        return view('pemesanan/detail', ['pesanan' => $pesanan, 'history' => $history]);
    }

    public function debug($id_bahan)
    {
        $suppliers = $this->supplierModel->findAll();
        $allSubs = $this->subKriteriaModel->findAll();
        
        // Ambil Skor dari Database
        $skorData = $this->nilaiSupplierBahanModel
            ->where('id_jenis_bahan', $id_bahan)
            ->findAll();

        // Mapping Data Skor
        $mapSkor = [];
        foreach($skorData as $sd) {
            $mapSkor[$sd['id_supplier']][$sd['id_sub_kriteria']] = $sd['nilai_skor'];
        }

        echo "<h1>Audit Perhitungan AHP (Debug Mode)</h1>";
        echo "<p>Bandingkan angka di bawah ini dengan Excel Anda.</p>";
        echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; font-family: sans-serif; font-size: 12px;'>";
        
        // Header (Nama Sub Kriteria + Bobot Global)
        echo "<tr style='background: #eee;'>";
        echo "<th>Supplier</th>";
        foreach($allSubs as $sub) {
            echo "<th style='text-align:center;'>";
            echo $sub['nama'] . "<br>";
            echo "<span style='color:blue;'>Bobot: " . number_format($sub['bobot_global'], 4) . "</span>";
            echo "</th>";
        }
        echo "<th>TOTAL SKOR</th>";
        echo "</tr>";

        // Body (Nilai Per Supplier)
        foreach ($suppliers as $s) {
            $totalSkor = 0;
            $id_sup = $s['id_supplier'];

            echo "<tr>";
            echo "<td><strong>" . $s['nama'] . "</strong></td>";

            foreach($allSubs as $sub) {
                $bobotGlobal = $sub['bobot_global']; 
                $skor = isset($mapSkor[$id_sup][$sub['id_sub_kriteria']]) ? $mapSkor[$id_sup][$sub['id_sub_kriteria']] : 0;
                
                $hasilKali = $bobotGlobal * $skor;
                $totalSkor += $hasilKali;

                // Tampilkan sel perhitungan: (Bobot x Skor)
                echo "<td style='text-align:center;'>";
                echo number_format($skor, 4) . "<br>";
                echo "<span style='color:green; font-size:10px;'>(" . number_format($hasilKali, 4) . ")</span>";
                echo "</td>";
            }
            
            // Total Akhir
            echo "<td style='background: #ffeb3b; font-weight:bold; text-align:right;'>" . number_format($totalSkor, 4) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}