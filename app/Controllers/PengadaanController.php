<?php
namespace App\Controllers;
use App\Models\PesananModel;
use App\Models\SupplierModel;
use App\Models\SubKriteriaModel;
use App\Models\NilaiSupplierBahanModel;
use App\Models\PesananHistoryAhpModel;

class PengadaanController extends BaseController {
    protected $pesananModel;
    protected $supplierModel;
    protected $subKriteriaModel;
    protected $nilaiSupplierBahanModel;
    protected $pesananHistoryAhpModel;

    public function __construct() {
        $this->pesananModel = new PesananModel();
        $this->supplierModel = new SupplierModel();
        $this->subKriteriaModel = new SubKriteriaModel();
        $this->nilaiSupplierBahanModel = new NilaiSupplierBahanModel();
        $this->pesananHistoryAhpModel = new PesananHistoryAhpModel();
    }

    public function index() {
        return view('pengadaan/dashboard', ['pending' => $this->pesananModel->getPesananLengkap('pending')]);
    }

    public function proses($id_pesanan) {
        $pesanan = $this->pesananModel->getPesananLengkap();
        $target = null;
        foreach($pesanan as $p) { if($p['id_pesanan'] == $id_pesanan) $target = $p; }
        return view('pengadaan/proses_ahp', ['pesanan' => $target]);
    }

    public function getLeaderboard($id_bahan) {
        $suppliers = $this->supplierModel->findAll();
        $leaderboard = [];
        $allSubs = $this->subKriteriaModel->findAll();
        $skorData = $this->nilaiSupplierBahanModel->where('id_jenis_bahan', $id_bahan)->findAll();
        
        $mapSkor = [];
        foreach($skorData as $sd) { $mapSkor[$sd['id_supplier']][$sd['id_sub_kriteria']] = $sd['nilai_skor']; }

        foreach ($suppliers as $s) {
            $totalSkor = 0;
            $id_sup = $s['id_supplier'];
            foreach($allSubs as $sub) {
                $skor = isset($mapSkor[$id_sup][$sub['id_sub_kriteria']]) ? $mapSkor[$id_sup][$sub['id_sub_kriteria']] : 0;
                $totalSkor += ($sub['bobot_global'] * $skor);
            }
            $leaderboard[] = ['id_supplier' => $id_sup, 'nama' => $s['nama'], 'skor_akhir' => $totalSkor];
        }
        usort($leaderboard, function ($a, $b) { return $b['skor_akhir'] <=> $a['skor_akhir']; });
        return $this->response->setJSON($leaderboard);
    }

    public function selesai() {
        $id_pesanan = $this->request->getPost('id_pesanan');
        $id_supplier = $this->request->getPost('id_supplier');
        $id_bahan = $this->request->getPost('id_jenis_bahan');

        // Update jadi Selesai
        $this->pesananModel->update($id_pesanan, ['id_supplier' => $id_supplier, 'status' => 'selesai']);

        // Simpan History AHP
        $leaderboard = json_decode($this->getLeaderboard($id_bahan)->getBody(), true);
        foreach($leaderboard as $rank => $row) {
            $this->pesananHistoryAhpModel->save([
                'id_pesanan' => $id_pesanan,
                'id_supplier' => $row['id_supplier'],
                'skor_ahp' => $row['skor_akhir'],
                'ranking' => $rank + 1
            ]);
        }
        return redirect()->to('/pengadaan')->with('success', 'Pesanan selesai diproses!');
    }
}