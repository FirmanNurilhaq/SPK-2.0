<?php

namespace App\Controllers;

use App\Models\SupplierModel;
use App\Models\SubKriteriaModel;
use App\Libraries\AhpCalculator;
use App\Controllers\BaseController;

class SupplierController extends BaseController
{
    protected $supplierModel;
    protected $subKriteriaModel;
    protected $db;
    protected $ahp;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->subKriteriaModel = new SubKriteriaModel();
        $this->db = \Config\Database::connect();
        $this->ahp = new AhpCalculator();
    }

    public function index()
    {
        // Ambil data sub kriteria untuk dropdown menu "Nilai Supplier"
        $subs = $this->subKriteriaModel->getSubWithKriteria();
        
        $data = [
            'supplier' => $this->supplierModel->findAll(),
            'subs' => $subs,
            'validation' => \Config\Services::validation()
        ];
        return view('supplier/index', $data);
    }

    public function store()
    {
        if (!$this->validate($this->supplierModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('error', 'Cek input supplier');
        }

        $this->supplierModel->save([
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'alamat' => $this->request->getPost('alamat'),
            'kontak' => $this->request->getPost('kontak'),
        ]);

        return redirect()->to('/supplier')->with('success', 'Supplier berhasil ditambahkan');
    }

    public function delete($id)
    {
        $this->supplierModel->delete($id);
        // Hapus data bobot terkait
        $this->db->table('supplier_bobot_sub')->where('id_supplier', $id)->delete();
        
        return redirect()->to('/supplier')->with('success', 'Supplier dihapus');
    }

    // --- SETUP PEMBOBOTAN SUPPLIER (Tanpa Simpan Matrix) ---

    public function bobot($id_sub_kriteria)
    {
        $sub = $this->subKriteriaModel->find($id_sub_kriteria);
        $suppliers = $this->supplierModel->findAll();

        if (count($suppliers) < 2) {
            return redirect()->to('/supplier')->with('error', 'Minimal harus ada 2 supplier untuk dibandingkan.');
        }

        // Matrix kosong (Reset Form setiap kali buka)
        $matrix = [];

        $data = [
            'sub' => $sub,
            'suppliers' => $suppliers,
            'matrix' => $matrix
        ];
        return view('supplier/bobot', $data);
    }

    public function updateMatrix()
    {
        $id_sub = $this->request->getPost('id_sub_kriteria');
        $nilai_pasangan = $this->request->getPost('nilai'); // Ambil input

        // 1. Siapkan Item Supplier
        $suppliers = $this->supplierModel->findAll();
        $items = [];
        foreach($suppliers as $s) $items[] = ['id' => $s['id_supplier']];

        // 2. Hitung Bobot Langsung (On-the-Fly)
        $bobotSupplier = $this->ahp->hitungBobot($nilai_pasangan, $items);

        // 3. Simpan Hasil Bobot ke tabel 'supplier_bobot_sub'
        // Ini WAJIB disimpan karena akan dipakai saat Pemesanan
        
        // Bersihkan dulu bobot lama untuk sub kriteria ini agar tidak duplikat
        $this->db->table('supplier_bobot_sub')->where('id_sub_kriteria', $id_sub)->delete();

        // Insert bobot baru
        foreach ($bobotSupplier as $id_supplier => $bobot) {
            $this->db->table('supplier_bobot_sub')->insert([
                'id_supplier' => $id_supplier,
                'id_sub_kriteria' => $id_sub,
                'bobot' => $bobot
            ]);
        }

        return redirect()->to('/supplier')->with('success', 'Penilaian Supplier berhasil disimpan!');
    }
}