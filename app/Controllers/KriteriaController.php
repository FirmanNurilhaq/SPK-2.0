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
            'bobot_global' => 0 // Default 0 sebelum dihitung
        ]);

        return redirect()->to('/kriteria')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function delete($id)
    {
        $this->kriteriaModel->delete($id);
        // Hapus data matriks terkait
        $this->db->table('kriteria_matrix')->where('kriteria_baris_id', $id)->orWhere('kriteria_kolom_id', $id)->delete();
        return redirect()->to('/kriteria')->with('success', 'Kriteria dihapus.');
    }

    // --- FITUR PEMBOBOTAN KRITERIA ---

    public function prioritas()
    {
        $kriteria = $this->kriteriaModel->findAll();
        
        // Ambil data matriks yang sudah tersimpan
        $matrixRaw = $this->db->table('kriteria_matrix')->get()->getResultArray();
        $matrix = [];
        foreach($matrixRaw as $row){
            $matrix[$row['kriteria_baris_id']][$row['kriteria_kolom_id']] = $row['nilai'];
        }

        $data = [
            'kriteria' => $kriteria,
            'matrix' => $matrix
        ];
        return view('kriteria/prioritas', $data);
    }

    public function updateMatrix()
    {
        $post = $this->request->getPost();
        $nilai_pasangan = $post['nilai']; // Array dari form

        // 1. Simpan ke Database
        // Format input name: nilai[id_baris][id_kolom]
        foreach ($nilai_pasangan as $id_baris => $kolom_data) {
            foreach ($kolom_data as $id_kolom => $nilai) {
                // Cek apakah sudah ada data
                $cek = $this->db->table('kriteria_matrix')
                    ->where('kriteria_baris_id', $id_baris)
                    ->where('kriteria_kolom_id', $id_kolom)
                    ->countAllResults();

                if ($cek > 0) {
                    $this->db->table('kriteria_matrix')
                        ->where('kriteria_baris_id', $id_baris)
                        ->where('kriteria_kolom_id', $id_kolom)
                        ->update(['nilai' => $nilai]);
                } else {
                    $this->db->table('kriteria_matrix')->insert([
                        'kriteria_baris_id' => $id_baris,
                        'kriteria_kolom_id' => $id_kolom,
                        'nilai' => $nilai
                    ]);
                }
            }
        }

        // 2. Hitung Ulang Bobot (Otomatis)
        $allKriteria = $this->kriteriaModel->findAll(); // Ambil data lagi biar fresh
        // Format ulang data matrix dari post untuk kalkulator
        $matrixData = $nilai_pasangan; 
        
        // Sesuaikan struktur array agar sesuai format Library
        $items = [];
        foreach($allKriteria as $k) $items[] = ['id' => $k['id_kriteria']];

        $bobotBaru = $this->ahp->hitungBobot($matrixData, $items);

        // 3. Update Bobot ke Tabel Kriteria
        foreach ($bobotBaru as $id => $bobot) {
            $this->kriteriaModel->update($id, ['bobot_global' => $bobot]);
        }

        // Juga update bobot global anak-anaknya (Sub Kriteria)
        // Karena Bobot Global Sub = Bobot Lokal Sub * Bobot Global Parent
        $this->recalcGlobalSub($id); 

        return redirect()->to('/kriteria')->with('success', 'Bobot Kriteria berhasil dihitung ulang!');
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
        
        // Ambil matrix sub
        $matrixRaw = $this->db->table('sub_kriteria_matrix')
            ->where('id_kriteria', $id_kriteria)
            ->get()->getResultArray();
            
        $matrix = [];
        foreach($matrixRaw as $row){
            $matrix[$row['sub_kriteria_baris_id']][$row['sub_kriteria_kolom_id']] = $row['nilai'];
        }

        $data = ['parent' => $parent, 'subs' => $subs, 'matrix' => $matrix];
        return view('kriteria/sub_prioritas', $data);
    }

    public function updateMatrixSub()
    {
        $id_kriteria = $this->request->getPost('id_kriteria_parent');
        $nilai_pasangan = $this->request->getPost('nilai');

        // 1. Simpan DB
        foreach ($nilai_pasangan as $id_baris => $kolom_data) {
            foreach ($kolom_data as $id_kolom => $nilai) {
                $where = [
                    'id_kriteria' => $id_kriteria,
                    'sub_kriteria_baris_id' => $id_baris,
                    'sub_kriteria_kolom_id' => $id_kolom
                ];
                
                if ($this->db->table('sub_kriteria_matrix')->where($where)->countAllResults() > 0) {
                    $this->db->table('sub_kriteria_matrix')->where($where)->update(['nilai' => $nilai]);
                } else {
                    $this->db->table('sub_kriteria_matrix')->insert(array_merge($where, ['nilai' => $nilai]));
                }
            }
        }

        // 2. Hitung Bobot Lokal
        $subs = $this->subKriteriaModel->where('id_kriteria', $id_kriteria)->findAll();
        $items = [];
        foreach($subs as $s) $items[] = ['id' => $s['id_sub_kriteria']];

        $bobotLokal = $this->ahp->hitungBobot($nilai_pasangan, $items);

        // 3. Update & Hitung Global
        $parent = $this->kriteriaModel->find($id_kriteria);
        
        foreach ($bobotLokal as $id_sub => $bobot) {
            $bobot_global = $bobot * $parent['bobot_global'];
            $this->subKriteriaModel->update($id_sub, [
                'bobot_lokal' => $bobot,
                'bobot_global' => $bobot_global
            ]);
        }

        return redirect()->to('/kriteria/detail/' . $id_kriteria)->with('success', 'Bobot Sub Kriteria berhasil dihitung!');
    }

    // Fungsi bantuan untuk update global sub saat parent berubah
    private function recalcGlobalSub($id_kriteria)
    {
        $parent = $this->kriteriaModel->find($id_kriteria);
        $subs = $this->subKriteriaModel->where('id_kriteria', $id_kriteria)->findAll();
        foreach($subs as $s){
            $new_global = $s['bobot_lokal'] * $parent['bobot_global'];
            $this->subKriteriaModel->update($s['id_sub_kriteria'], ['bobot_global' => $new_global]);
        }
    }
}