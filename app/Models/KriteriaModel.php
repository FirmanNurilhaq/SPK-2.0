<?php
namespace App\Models;
use CodeIgniter\Model;

class KriteriaModel extends Model {
    protected $table = 'kriteria';
    protected $primaryKey = 'id_kriteria';
    // TAMBAHKAN 'bobot_global' DISINI:
    protected $allowedFields = ['kode', 'nama', 'bobot_global']; 
}