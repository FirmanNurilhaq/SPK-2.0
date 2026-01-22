<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KriteriaModel;
use App\Models\SubKriteriaModel;
use App\Libraries\AhpCalculator;

class KriteriaController extends BaseController
{
    protected $kriteriaModel;
    protected $subKriteriaModel;
    protected $db;
    protected $ahp;

    public function __construct()
    {
        $this->kriteriaModel = new KriteriaModel();
        $this->subKriteriaModel = new SubKriteriaModel();
        $this->db = \Config\Database::connect();
        $this->ahp = new AhpCalculator();
    }

    // ==========================================
    // BAGIAN 1: KRITERIA UTAMA (PARENT)
    // ==========================================

    public function index()
    {
        $data = [
            'kriteria' => $this->kriteriaModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('kriteria/index', $data);
    }

    public function store()
    {
        if (!$this->validate($this->kriteriaModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambah kriteria. Cek inputan.');
        }

        $this->kriteriaModel->save([
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'bobot_global' => 0 
        ]);

        return redirect()->to('/kriteria')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function delete($id)
    {
        $this->kriteriaModel->delete($id);
        // Hapus Sub Kriteria terkait otomatis (Cascade di DB), tapi kita pastikan bersih
        $this->subKriteriaModel->where('id_kriteria', $id)->delete();
        return redirect()->to('/kriteria')->with('success', 'Kriteria dihapus.');
    }

    // --- FITUR PEMBOBOTAN KRITERIA (Tanpa Simpan Matrix) ---

    public function prioritas()
    {
        $kriteria = $this->kriteriaModel->findAll();
        
        // Kita kirim array kosong karena tidak menyimpan history input
        // View akan otomatis mereset form ke "1" (Sama Penting)
        $matrix = []; 

        $data = [
            'kriteria' => $kriteria,
            'matrix' => $matrix
        ];
        return view('kriteria/prioritas', $data);
    }

    public function updateMatrix()
    {
        // 1. Ambil Input dari Form
        $nilai_pasangan = $this->request->getPost('nilai'); 

        // 2. Siapkan ID Item untuk Kalkulator
        $allKriteria = $this->kriteriaModel->findAll();
        $items = [];
        foreach($allKriteria as $k) $items[] = ['id' => $k['id_kriteria']];

        // 3. Hitung Bobot Langsung (On-the-Fly)
        // Library AhpCalculator akan memproses array input user
        $bobotBaru = $this->ahp->hitungBobot($nilai_pasangan, $items);

        // 4. Update Hasil Bobot ke Tabel Kriteria
        foreach ($bobotBaru as $id => $bobot) {
            $this->kriteriaModel->update($id, ['bobot_global' => $bobot]);
        }

        // 5. Hitung Ulang Global Bobot Anak-anaknya (Sub Kriteria)
        // Karena jika bobot ortu berubah, bobot global anak juga berubah
        $this->recalcGlobalSubAll(); 

        return redirect()->to('/kriteria')->with('success', 'Bobot Kriteria berhasil dihitung dan diperbarui!');
    }


    // ==========================================
    // BAGIAN 2: SUB KRITERIA (CHILD)
    // ==========================================

    public function detail($id_kriteria)
    {
        $parent = $this->kriteriaModel->find($id_kriteria);
        $subs = $this->subKriteriaModel->where('id_kriteria', $id_kriteria)->findAll();

        $data = [
            'parent' => $parent,
            'subs' => $subs
        ];
        return view('kriteria/sub_index', $data);
    }

    public function storeSub()
    {
        $id_kriteria = $this->request->getPost('id_kriteria');
        $this->subKriteriaModel->save([
            'id_kriteria' => $id_kriteria,
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'bobot_lokal' => 0,
            'bobot_global' => 0
        ]);
        return redirect()->to('/kriteria/detail/' . $id_kriteria)->with('success', 'Sub Kriteria ditambahkan.');
    }

    public function prioritasSub($id_kriteria)
    {
        $parent = $this->kriteriaModel->find($id_kriteria);
        $subs = $this->subKriteriaModel->where('id_kriteria', $id_kriteria)->findAll();
        
        // Matrix kosong (Reset form)
        $matrix = [];

        $data = ['parent' => $parent, 'subs' => $subs, 'matrix' => $matrix];
        return view('kriteria/sub_prioritas', $data);
    }

    public function updateMatrixSub()
    {
        $id_kriteria_parent = $this->request->getPost('id_kriteria_parent');
        $nilai_pasangan = $this->request->getPost('nilai');

        // 1. Siapkan Item
        $subs = $this->subKriteriaModel->where('id_kriteria', $id_kriteria_parent)->findAll();
        $items = [];
        foreach($subs as $s) $items[] = ['id' => $s['id_sub_kriteria']];

        // 2. Hitung Bobot Lokal Langsung
        $bobotLokal = $this->ahp->hitungBobot($nilai_pasangan, $items);

        // 3. Ambil Bobot Parent untuk hitung Global
        $parent = $this->kriteriaModel->find($id_kriteria_parent);
        
        // 4. Update ke Tabel Sub Kriteria
        foreach ($bobotLokal as $id_sub => $bobot) {
            $bobot_global = $bobot * $parent['bobot_global'];
            $this->subKriteriaModel->update($id_sub, [
                'bobot_lokal' => $bobot,
                'bobot_global' => $bobot_global
            ]);
        }

        return redirect()->to('/kriteria/detail/' . $id_kriteria_parent)->with('success', 'Bobot Sub Kriteria berhasil dihitung!');
    }

    // Helper: Update semua global sub saat parent berubah
    private function recalcGlobalSubAll()
    {
        $subs = $this->subKriteriaModel->findAll();
        foreach($subs as $s){
            $parent = $this->kriteriaModel->find($s['id_kriteria']);
            if($parent) {
                $new_global = $s['bobot_lokal'] * $parent['bobot_global'];
                $this->subKriteriaModel->update($s['id_sub_kriteria'], ['bobot_global' => $new_global]);
            }
        }
    }
}