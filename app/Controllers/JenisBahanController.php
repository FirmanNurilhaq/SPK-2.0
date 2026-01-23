<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JenisBahanModel;
use App\Models\SubKriteriaModel;
use App\Models\SupplierModel;
use App\Models\NilaiSupplierBahanModel;
use App\Libraries\AhpCalculator;

class JenisBahanController extends BaseController
{
    protected $jenisBahanModel;
    protected $subKriteriaModel;
    protected $supplierModel;
    protected $nilaiSupplierBahanModel;
    protected $ahp;

    public function __construct()
    {
        $this->jenisBahanModel = new JenisBahanModel();
        $this->subKriteriaModel = new SubKriteriaModel();
        $this->supplierModel = new SupplierModel();
        $this->nilaiSupplierBahanModel = new NilaiSupplierBahanModel();
        $this->ahp = new AhpCalculator();
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
        return redirect()->to('/jenis-bahan')->with('success', 'Data Bahan disimpan.');
    }

    public function delete($id)
    {
        $this->jenisBahanModel->delete($id);
        return redirect()->to('/jenis-bahan');
    }

    // --- DASHBOARD SETUP AHP PER BAHAN ---
    public function setup($id_bahan)
    {
        $bahan = $this->jenisBahanModel->find($id_bahan);
        $sub = $this->subKriteriaModel->getSubLengkap(); // Mengambil Sub + Nama Parent
        
        $data = [
            'bahan' => $bahan,
            'sub' => $sub
        ];
        return view('jenis_bahan/setup', $data);
    }

    // --- C. SETUP NILAI SUPPLIER (DINAMIS PER BAHAN) ---
    public function setupSupplier($id_bahan, $id_sub)
    {
        $bahan = $this->jenisBahanModel->find($id_bahan);
        $sub = $this->subKriteriaModel->find($id_sub);
        $suppliers = $this->supplierModel->findAll();
        
        if(count($suppliers) < 2) return redirect()->back()->with('error', 'Minimal 2 supplier.');

        $data = ['bahan' => $bahan, 'sub' => $sub, 'suppliers' => $suppliers];
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

        // 1. Hitung Skor Supplier (AHP)
        $skorSupplier = $this->ahp->hitungBobot($nilai_pasangan, $items);

        // 2. Bersihkan skor lama untuk (Bahan + Sub) ini
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
}