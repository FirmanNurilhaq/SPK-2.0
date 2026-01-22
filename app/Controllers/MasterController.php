<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KriteriaModel;
use App\Models\SubKriteriaModel;
use App\Models\SupplierModel;

class MasterController extends BaseController
{
    protected $kriteriaModel;
    protected $subKriteriaModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->kriteriaModel = new KriteriaModel();
        $this->subKriteriaModel = new SubKriteriaModel();
        $this->supplierModel = new SupplierModel();
    }

    // --- KRITERIA ---
    public function kriteria()
    {
        $data = [
            'kriteria' => $this->kriteriaModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('master/kriteria', $data);
    }

    public function storeKriteria()
    {
        $this->kriteriaModel->save([
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama')
        ]);
        return redirect()->to('/master/kriteria')->with('success', 'Data Kriteria disimpan.');
    }

    public function deleteKriteria($id)
    {
        $this->kriteriaModel->delete($id);
        return redirect()->to('/master/kriteria')->with('success', 'Data dihapus.');
    }

    // --- SUB KRITERIA ---
    public function subKriteria($id_kriteria)
    {
        $parent = $this->kriteriaModel->find($id_kriteria);
        $subs = $this->subKriteriaModel->where('id_kriteria', $id_kriteria)->findAll();
        
        $data = ['parent' => $parent, 'subs' => $subs];
        return view('master/sub_kriteria', $data);
    }

    public function storeSub()
    {
        $id = $this->request->getPost('id_kriteria');
        $this->subKriteriaModel->save([
            'id_kriteria' => $id,
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama')
        ]);
        return redirect()->to('/master/sub/' . $id)->with('success', 'Sub Kriteria disimpan.');
    }

    public function deleteSub($id)
    {
        $sub = $this->subKriteriaModel->find($id);
        $this->subKriteriaModel->delete($id);
        return redirect()->to('/master/sub/' . $sub['id_kriteria'])->with('success', 'Data dihapus.');
    }

    // --- SUPPLIER ---
    public function supplier()
    {
        $data = ['supplier' => $this->supplierModel->findAll()];
        return view('master/supplier', $data);
    }

    public function storeSupplier()
    {
        $this->supplierModel->save([
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'alamat' => $this->request->getPost('alamat'),
            'kontak' => $this->request->getPost('kontak'),
        ]);
        return redirect()->to('/master/supplier')->with('success', 'Supplier disimpan.');
    }

    public function deleteSupplier($id)
    {
        $this->supplierModel->delete($id);
        return redirect()->to('/master/supplier')->with('success', 'Data dihapus.');
    }
}