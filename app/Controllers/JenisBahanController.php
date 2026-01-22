<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JenisBahanModel;
use App\Models\KriteriaModel;
use App\Models\SubKriteriaModel;
use App\Models\SupplierModel;
use App\Models\NilaiKriteriaBahanModel;
use App\Models\NilaiSubBahanModel;
use App\Models\NilaiSupplierBahanModel;
use App\Libraries\AhpCalculator;

class JenisBahanController extends BaseController
{
    protected $jenisBahanModel;
    protected $kriteriaModel;
    protected $subKriteriaModel;
    protected $supplierModel;
    protected $nilaiKriteriaBahanModel;
    protected $nilaiSubBahanModel;
    protected $nilaiSupplierBahanModel;
    protected $ahp;
    protected $db;

    public function __construct()
    {
        $this->jenisBahanModel = new JenisBahanModel();
        $this->kriteriaModel = new KriteriaModel();
        $this->subKriteriaModel = new SubKriteriaModel();
        $this->supplierModel = new SupplierModel();
        
        $this->nilaiKriteriaBahanModel = new NilaiKriteriaBahanModel();
        $this->nilaiSubBahanModel = new NilaiSubBahanModel();
        $this->nilaiSupplierBahanModel = new NilaiSupplierBahanModel();
        
        $this->ahp = new AhpCalculator();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = ['bahan' => $this->jenisBahanModel->findAll()];
        return view('jenis_bahan/index', $data);
    }

    public function store()
    {
        $this->jenisBahanModel->save([
            'nama_bahan' => $this->request->getPost('nama_bahan'),
            'keterangan' => $this->request->getPost('keterangan')
        ]);
        return redirect()->to('/jenis-bahan')->with('success', 'Jenis Bahan ditambahkan.');
    }

    public function delete($id)
    {
        $this->jenisBahanModel->delete($id);
        // Cascade delete di database akan otomatis menghapus bobot terkait
        return redirect()->to('/jenis-bahan')->with('success', 'Jenis Bahan dihapus.');
    }

    // --- DASHBOARD SETUP AHP PER BAHAN ---
    public function setup($id_bahan)
    {
        $bahan = $this->jenisBahanModel->find($id_bahan);
        $kriteria = $this->kriteriaModel->findAll();
        $sub = $this->subKriteriaModel->getSubLengkap();
        
        // Cek status kelengkapan data (untuk UI)
        $statusKriteria = $this->nilaiKriteriaBahanModel->where('id_jenis_bahan', $id_bahan)->countAllResults() > 0;
        
        $data = [
            'bahan' => $bahan,
            'kriteria' => $kriteria,
            'sub' => $sub,
            'statusKriteria' => $statusKriteria
        ];
        return view('jenis_bahan/setup', $data);
    }

    // --- A. PEMBOBOTAN KRITERIA ---
    public function setupKriteria($id_bahan)
    {
        $bahan = $this->jenisBahanModel->find($id_bahan);
        $kriteria = $this->kriteriaModel->findAll();
        
        // Matrix kosong (On-the-fly)
        $matrix = [];

        $data = ['bahan' => $bahan, 'kriteria' => $kriteria, 'matrix' => $matrix];
        return view('jenis_bahan/setup_kriteria', $data);
    }

    public function saveKriteria()
    {
        $id_bahan = $this->request->getPost('id_jenis_bahan');
        $nilai_pasangan = $this->request->getPost('nilai');

        $kriteria = $this->kriteriaModel->findAll();
        $items = [];
        foreach($kriteria as $k) $items[] = ['id' => $k['id_kriteria']];

        // 1. Hitung Bobot
        $bobotBaru = $this->ahp->hitungBobot($nilai_pasangan, $items);

        // 2. Hapus bobot lama untuk bahan ini
        $this->nilaiKriteriaBahanModel->where('id_jenis_bahan', $id_bahan)->delete();

        // 3. Simpan baru ke tabel nilai_kriteria_bahan
        foreach($bobotBaru as $id_k => $val) {
            $this->nilaiKriteriaBahanModel->insert([
                'id_jenis_bahan' => $id_bahan,
                'id_kriteria' => $id_k,
                'nilai_bobot' => $val
            ]);
        }

        // Trigger update global sub
        $this->recalcGlobalSub($id_bahan);

        return redirect()->to("/jenis-bahan/setup/$id_bahan")->with('success', 'Bobot Kriteria untuk bahan ini tersimpan.');
    }

    // --- B. PEMBOBOTAN SUB KRITERIA ---
    public function setupSub($id_bahan, $id_kriteria_parent)
    {
        $bahan = $this->jenisBahanModel->find($id_bahan);
        $parent = $this->kriteriaModel->find($id_kriteria_parent);
        $subs = $this->subKriteriaModel->where('id_kriteria', $id_kriteria_parent)->findAll();
        $matrix = [];

        $data = ['bahan' => $bahan, 'parent' => $parent, 'subs' => $subs, 'matrix' => $matrix];
        return view('jenis_bahan/setup_sub', $data);
    }

    public function saveSub()
    {
        $id_bahan = $this->request->getPost('id_jenis_bahan');
        $id_parent = $this->request->getPost('id_kriteria_parent');
        $nilai_pasangan = $this->request->getPost('nilai');

        $subs = $this->subKriteriaModel->where('id_kriteria', $id_parent)->findAll();
        $items = [];
        foreach($subs as $s) $items[] = ['id' => $s['id_sub_kriteria']];

        // 1. Hitung Bobot Lokal
        $bobotLokal = $this->ahp->hitungBobot($nilai_pasangan, $items);

        // 2. Ambil Bobot Parent (Kriteria) pada Bahan ini
        $kriteriaVal = $this->nilaiKriteriaBahanModel
            ->where('id_jenis_bahan', $id_bahan)
            ->where('id_kriteria', $id_parent)
            ->first();
        
        $bobotParent = $kriteriaVal ? $kriteriaVal['nilai_bobot'] : 0;

        // 3. Simpan (Hapus lama dulu per sub yang terkait parent ini)
        // Kita loop per item untuk update/insert
        foreach($bobotLokal as $id_sub => $valLokal) {
            $valGlobal = $valLokal * $bobotParent;

            // Cek exist
            $exist = $this->nilaiSubBahanModel
                ->where('id_jenis_bahan', $id_bahan)
                ->where('id_sub_kriteria', $id_sub)
                ->first();

            if($exist) {
                $this->nilaiSubBahanModel->update($exist['id_nilai_sub'], [
                    'nilai_bobot_lokal' => $valLokal,
                    'nilai_bobot_global' => $valGlobal
                ]);
            } else {
                $this->nilaiSubBahanModel->insert([
                    'id_jenis_bahan' => $id_bahan,
                    'id_sub_kriteria' => $id_sub,
                    'nilai_bobot_lokal' => $valLokal,
                    'nilai_bobot_global' => $valGlobal
                ]);
            }
        }

        return redirect()->to("/jenis-bahan/setup/$id_bahan")->with('success', 'Bobot Sub Kriteria tersimpan.');
    }

    // --- C. PENILAIAN SUPPLIER ---
    public function setupSupplier($id_bahan, $id_sub)
    {
        $bahan = $this->jenisBahanModel->find($id_bahan);
        $sub = $this->subKriteriaModel->find($id_sub);
        $suppliers = $this->supplierModel->findAll();
        $matrix = [];

        if(count($suppliers) < 2) return redirect()->back()->with('error', 'Minimal 2 supplier.');

        $data = ['bahan' => $bahan, 'sub' => $sub, 'suppliers' => $suppliers, 'matrix' => $matrix];
        return view('jenis_bahan/setup_supplier', $data);
    }

    public function saveSupplier()
    {
        $id_bahan = $this->request->getPost('id_jenis_bahan');
        $id_sub = $this->request->getPost('id_sub_kriteria');
        $nilai_pasangan = $this->request->getPost('nilai');

        $suppliers = $this->supplierModel->findAll();
        $items = [];
        foreach($suppliers as $s) $items[] = ['id' => $s['id_supplier']];

        // 1. Hitung Skor Supplier
        $skorSupplier = $this->ahp->hitungBobot($nilai_pasangan, $items);

        // 2. Bersihkan skor lama untuk kombinasi Bahan & Sub ini
        $this->nilaiSupplierBahanModel
            ->where('id_jenis_bahan', $id_bahan)
            ->where('id_sub_kriteria', $id_sub)
            ->delete();

        // 3. Simpan baru
        foreach($skorSupplier as $id_sup => $skor) {
            $this->nilaiSupplierBahanModel->insert([
                'id_jenis_bahan' => $id_bahan,
                'id_supplier' => $id_sup,
                'id_sub_kriteria' => $id_sub,
                'nilai_skor' => $skor
            ]);
        }

        return redirect()->to("/jenis-bahan/setup/$id_bahan")->with('success', 'Skor Supplier tersimpan.');
    }

    // Helper: Update global sub jika bobot kriteria berubah
    private function recalcGlobalSub($id_bahan)
    {
        $allNilaiSub = $this->nilaiSubBahanModel->where('id_jenis_bahan', $id_bahan)->findAll();
        foreach($allNilaiSub as $ns) {
            $subData = $this->subKriteriaModel->find($ns['id_sub_kriteria']);
            
            // Ambil bobot parent baru
            $parentVal = $this->nilaiKriteriaBahanModel
                ->where('id_jenis_bahan', $id_bahan)
                ->where('id_kriteria', $subData['id_kriteria'])
                ->first();
            
            $bobotParent = $parentVal ? $parentVal['nilai_bobot'] : 0;
            $newGlobal = $ns['nilai_bobot_lokal'] * $bobotParent;

            $this->nilaiSubBahanModel->update($ns['id_nilai_sub'], ['nilai_bobot_global' => $newGlobal]);
        }
    }
}