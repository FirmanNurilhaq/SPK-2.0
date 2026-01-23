<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KriteriaModel;
use App\Models\SubKriteriaModel;
use App\Models\SupplierModel;
use App\Libraries\AhpCalculator; // Load Library AHP

class MasterController extends BaseController
{
    protected $kriteriaModel;
    protected $subKriteriaModel;
    protected $supplierModel;
    protected $ahp;

    public function __construct()
    {
        $this->kriteriaModel = new KriteriaModel();
        $this->subKriteriaModel = new SubKriteriaModel();
        $this->supplierModel = new SupplierModel();
        $this->ahp = new AhpCalculator();
    }

    // --- KRITERIA CRUD ---
    public function kriteria()
    {
        $data = ['kriteria' => $this->kriteriaModel->findAll()];
        return view('master/kriteria', $data);
    }

    public function storeKriteria()
    {
        $this->kriteriaModel->save([
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'bobot_global' => 0 // Default 0
        ]);
        return redirect()->to('/master/kriteria')->with('success', 'Kriteria disimpan.');
    }

    public function deleteKriteria($id)
    {
        $this->kriteriaModel->delete($id);
        $this->subKriteriaModel->where('id_kriteria', $id)->delete();
        return redirect()->to('/master/kriteria')->with('success', 'Data dihapus.');
    }

    // --- AHP KRITERIA (GLOBAL) ---
    public function prioritasKriteria()
    {
        $kriteria = $this->kriteriaModel->findAll();
        // Matrix kosong (Reset)
        $data = ['kriteria' => $kriteria, 'matrix' => []];
        return view('master/prioritas_kriteria', $data);
    }

    public function savePrioritasKriteria()
    {
        $nilai_pasangan = $this->request->getPost('nilai');
        $kriteria = $this->kriteriaModel->findAll();
        
        $items = [];
        foreach($kriteria as $k) $items[] = ['id' => $k['id_kriteria']];

        // Hitung Bobot Global
        $bobotBaru = $this->ahp->hitungBobot($nilai_pasangan, $items);

        // Update ke Tabel Master Kriteria
        foreach($bobotBaru as $id => $val) {
            $this->kriteriaModel->update($id, ['bobot_global' => $val]);
        }

        // Hitung ulang bobot global sub kriteria (karena bobot ortu berubah)
        $this->recalcGlobalSubAll();

        return redirect()->to('/master/kriteria')->with('success', 'Bobot Global Kriteria berhasil diupdate!');
    }

    // --- SUB KRITERIA CRUD ---
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
            'nama' => $this->request->getPost('nama'),
            'bobot_lokal' => 0,
            'bobot_global' => 0
        ]);
        return redirect()->to('/master/sub/' . $id)->with('success', 'Sub Kriteria disimpan.');
    }

    public function deleteSub($id)
    {
        $sub = $this->subKriteriaModel->find($id);
        $this->subKriteriaModel->delete($id);
        return redirect()->to('/master/sub/' . $sub['id_kriteria']);
    }

    // --- AHP SUB KRITERIA (GLOBAL) ---
    public function prioritasSub($id_kriteria)
    {
        $parent = $this->kriteriaModel->find($id_kriteria);
        $subs = $this->subKriteriaModel->where('id_kriteria', $id_kriteria)->findAll();
        $data = ['parent' => $parent, 'subs' => $subs, 'matrix' => []];
        return view('master/prioritas_sub', $data);
    }

    public function savePrioritasSub()
    {
        $id_parent = $this->request->getPost('id_kriteria_parent');
        $nilai_pasangan = $this->request->getPost('nilai');
        
        $subs = $this->subKriteriaModel->where('id_kriteria', $id_parent)->findAll();
        $items = [];
        foreach($subs as $s) $items[] = ['id' => $s['id_sub_kriteria']];

        // Hitung Bobot Lokal
        $bobotLokal = $this->ahp->hitungBobot($nilai_pasangan, $items);
        
        // Ambil Bobot Ortu
        $parent = $this->kriteriaModel->find($id_parent);

        // Update Tabel Sub Kriteria
        foreach($bobotLokal as $id_sub => $valLokal) {
            $valGlobal = $valLokal * $parent['bobot_global'];
            $this->subKriteriaModel->update($id_sub, [
                'bobot_lokal' => $valLokal,
                'bobot_global' => $valGlobal
            ]);
        }

        return redirect()->to('/master/sub/' . $id_parent)->with('success', 'Bobot Sub Kriteria berhasil diupdate!');
    }

    // --- SUPPLIER CRUD ---
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
        return redirect()->to('/master/supplier');
    }

    public function deleteSupplier($id)
    {
        $this->supplierModel->delete($id);
        return redirect()->to('/master/supplier');
    }

    // Helper Recalculate Global
    private function recalcGlobalSubAll() {
        $subs = $this->subKriteriaModel->findAll();
        foreach($subs as $s) {
            $parent = $this->kriteriaModel->find($s['id_kriteria']);
            if($parent) {
                $newGlobal = $s['bobot_lokal'] * $parent['bobot_global'];
                $this->subKriteriaModel->update($s['id_sub_kriteria'], ['bobot_global' => $newGlobal]);
            }
        }
    }
}