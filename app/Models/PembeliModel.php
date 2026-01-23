<?php
namespace App\Models;
use CodeIgniter\Model;

class PembeliModel extends Model {
    protected $table = 'pembeli';
    protected $primaryKey = 'id_pembeli';
    protected $allowedFields = ['nama_pembeli', 'alamat', 'kontak'];
}