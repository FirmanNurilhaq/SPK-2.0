<?php

namespace App\Controllers;

use App\Models\SupplierModel;
use App\Models\SubKriteriaModel;
use App\Libraries\AhpCalculator;

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
        // Hapus data terkait
        $this->db->table('supplier_matrix')->where('supplier_baris_id', $id)->orWhere('supplier_kolom_id', $id)->delete();
        $this->db->table('supplier_bobot_sub')->where('id_supplier', $id)->delete();
        
        return redirect()->to('/supplier')->with('success', 'Supplier dihapus');
    }

    // --- SETUP PEMBOBOTAN SUPPLIER ---

    public function bobot($id_sub_kriteria)
    {
        $sub = $this->subKriteriaModel->find($id_sub_kriteria);
        $suppliers = $this->supplierModel->findAll();

        if (count($suppliers) < 2) {
            return redirect()->to('/supplier')->with('error', 'Minimal harus ada 2 supplier untuk dibandingkan.');
        }

        // Ambil matrix
        $matrixRaw = $this->db->table('supplier_matrix')
            ->where('id_sub_kriteria', $id_sub_kriteria)
            ->get()->getResultArray();

        $matrix = [];
        foreach($matrixRaw as $row){
            $matrix[$row['supplier_baris_id']][$row['supplier_kolom_id']] = $row['nilai'];
        }

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
        $nilai_pasangan = $this->request->getPost('nilai');

        // 1. Simpan Matrix
        foreach ($nilai_pasangan as $id_baris => $kolom_data) {
            foreach ($kolom_data as $id_kolom => $nilai) {
                $where = [
                    'id_sub_kriteria' => $id_sub,
                    'supplier_baris_id' => $id_baris,
                    'supplier_kolom_id' => $id_kolom
                ];
                
                if ($this->db->table('supplier_matrix')->where($where)->countAllResults() > 0) {
                    $this->db->table('supplier_matrix')->where($where)->update(['nilai' => $nilai]);
                } else {
                    $this->db->table('supplier_matrix')->insert(array_merge($where, ['nilai' => $nilai]));
                }
            }
        }

        // 2. Hitung Bobot Supplier untuk Sub Kriteria ini
        $suppliers = $this->supplierModel->findAll();
        $items = [];
        foreach($suppliers as $s) $items[] = ['id' => $s['id_supplier']];

        $bobotSupplier = $this->ahp->hitungBobot($nilai_pasangan, $items);

        // 3. Simpan Bobot ke tabel supplier_bobot_sub
        foreach ($bobotSupplier as $id_supplier => $bobot) {
            $where = ['id_supplier' => $id_supplier, 'id_sub_kriteria' => $id_sub];
            
            if ($this->db->table('supplier_bobot_sub')->where($where)->countAllResults() > 0) {
                $this->db->table('supplier_bobot_sub')->where($where)->update(['bobot' => $bobot]);
            } else {
                $this->db->table('supplier_bobot_sub')->insert([
                    'id_supplier' => $id_supplier,
                    'id_sub_kriteria' => $id_sub,
                    'bobot' => $bobot
                ]);
            }
        }

        return redirect()->to('/supplier')->with('success', 'Bobot Supplier untuk kriteria ini berhasil diupdate.');
    }
}