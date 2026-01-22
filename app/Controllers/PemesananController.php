<?php

namespace App\Controllers;

use App\Models\PesananModel;
use App\Models\SupplierModel;
use App\Models\SubKriteriaModel;

class PemesananController extends BaseController
{
    protected $pesananModel;
    protected $supplierModel;
    protected $subKriteriaModel;
    protected $db;

    public function __construct()
    {
        $this->pesananModel = new PesananModel();
        $this->supplierModel = new SupplierModel();
        $this->subKriteriaModel = new SubKriteriaModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'history' => $this->pesananModel->getHistoryLengkap()
        ];
        return view('pemesanan/index', $data);
    }

    public function create()
    {
        // 1. Hitung Live AHP Leaderboard
        $leaderboard = $this->hitungLeaderboardSaatIni();

        $data = [
            'leaderboard' => $leaderboard
        ];
        return view('pemesanan/create', $data);
    }

    public function store()
    {
        // 1. Simpan Transaksi Pesanan
        $dataPesanan = [
            'jumlah_lusin' => $this->request->getPost('jumlah_lusin'),
            'bahan_baku' => $this->request->getPost('bahan_baku'),
            'catatan' => $this->request->getPost('catatan'),
            'id_supplier_terpilih' => $this->request->getPost('id_supplier_terpilih'), // User memilih berdasarkan rekomendasi
            'tanggal' => date('Y-m-d H:i:s')
        ];
        
        $this->pesananModel->insert($dataPesanan);
        $id_pesanan = $this->pesananModel->getInsertID();

        // 2. Simpan Snapshot Leaderboard ke History (Agar data abadi)
        $leaderboard = $this->hitungLeaderboardSaatIni(); // Hitung ulang untuk memastikan data paling baru
        
        foreach ($leaderboard as $rank => $row) {
            $this->db->table('pesanan_ahp_history')->insert([
                'id_pesanan' => $id_pesanan,
                'id_supplier' => $row['id_supplier'],
                'skor_ahp' => $row['skor_akhir'],
                'ranking' => $rank + 1
            ]);
        }

        return redirect()->to('/pemesanan')->with('success', 'Pesanan berhasil dibuat dan history AHP tersimpan.');
    }

    public function detail($id)
    {
        // Ambil Data Pesanan
        $pesanan = $this->pesananModel->getHistoryLengkap($id);
        
        // Ambil Snapshot History (BUKAN hitung ulang)
        $history = $this->db->table('pesanan_ahp_history')
            ->select('pesanan_ahp_history.*, supplier.nama as nama_supplier')
            ->join('supplier', 'supplier.id_supplier = pesanan_ahp_history.id_supplier')
            ->where('id_pesanan', $id)
            ->orderBy('ranking', 'ASC')
            ->get()->getResultArray();

        $data = [
            'pesanan' => $pesanan,
            'history' => $history
        ];
        return view('pemesanan/detail', $data);
    }

    // --- LOGIKA INTI PENJUMLAHAN BOBOT AHP ---
    private function hitungLeaderboardSaatIni()
    {
        $suppliers = $this->supplierModel->findAll();
        $subs = $this->subKriteriaModel->findAll();
        $leaderboard = [];

        foreach ($suppliers as $s) {
            $totalSkor = 0;
            
            // Loop setiap sub kriteria
            foreach ($subs as $sub) {
                // Ambil bobot global sub kriteria (Wj)
                $bobotGlobalSub = $sub['bobot_global'];

                // Ambil bobot supplier terhadap sub ini (Sij)
                $query = $this->db->table('supplier_bobot_sub')
                    ->where('id_supplier', $s['id_supplier'])
                    ->where('id_sub_kriteria', $sub['id_sub_kriteria'])
                    ->get()->getRowArray();
                
                $bobotSupplier = $query ? $query['bobot'] : 0;

                // Rumus: Skor = Sum(BobotGlobalSub * BobotSupplier)
                $totalSkor += ($bobotGlobalSub * $bobotSupplier);
            }

            $leaderboard[] = [
                'id_supplier' => $s['id_supplier'],
                'nama' => $s['nama'],
                'skor_akhir' => $totalSkor
            ];
        }

        // Urutkan dari skor tertinggi (Descending)
        usort($leaderboard, function ($a, $b) {
            return $b['skor_akhir'] <=> $a['skor_akhir'];
        });

        return $leaderboard;
    }
}