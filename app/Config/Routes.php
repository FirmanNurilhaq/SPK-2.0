<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =========================================================================
// 1. PUBLIC ROUTES (LOGIN & LOGOUT)
// =========================================================================
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('auth/process', 'AuthController::process');
$routes->get('logout', 'AuthController::logout');


// =========================================================================
// 2. GROUP: BAGIAN PEMESANAN (SALES)
// Hak Akses: Input Data Pembeli & Membuat Pesanan Baru
// =========================================================================
$routes->group('pemesanan', ['filter' => 'auth'], function($routes) {
    // Dashboard Sales (History Pesanan)
    $routes->get('/', 'PemesananController::index'); 
    
    // Kelola Data Pembeli (CRUD Sederhana)
    $routes->get('pembeli', 'PemesananController::pembeli');
    $routes->post('pembeli/store', 'PemesananController::storePembeli');
    
    // Input Pesanan Baru
    $routes->get('create', 'PemesananController::create');
    $routes->post('store', 'PemesananController::store');
});


// =========================================================================
// 3. GROUP: BAGIAN PENGADAAN (GUDANG / ADMIN AHP)
// Hak Akses: Proses AHP & Pilih Supplier
// =========================================================================
$routes->group('pengadaan', ['filter' => 'auth'], function($routes) {
    // Dashboard Pending (Daftar Pesanan Masuk)
    $routes->get('/', 'PengadaanController::index');
    
    // --- PROSES SELEKSI SUPPLIER (AHP) ---
    $routes->get('proses/(:num)', 'PengadaanController::proses/$1'); // Halaman Proses per Pesanan
    $routes->get('get-leaderboard/(:num)', 'PengadaanController::getLeaderboard/$1'); // API JSON untuk JS
    $routes->post('selesai', 'PengadaanController::selesai'); // Simpan Keputusan Akhir
});


// =========================================================================
// 4. GROUP: MASTER DATA (GLOBAL / ADMIN)
// Perbaikan: Dikeluarkan dari grup 'pengadaan' agar URL sesuai Navbar
// URL: localhost:8080/master/kriteria
// =========================================================================
$routes->group('master', ['filter' => 'auth'], function($routes) {
    // --- Kriteria ---
    $routes->get('kriteria', 'MasterController::kriteria');
    $routes->post('kriteria/store', 'MasterController::storeKriteria');
    $routes->get('kriteria/delete/(:num)', 'MasterController::deleteKriteria/$1');
    
    // Hitung Bobot Kriteria
    $routes->get('kriteria/prioritas', 'MasterController::prioritasKriteria'); 
    $routes->post('kriteria/save-prioritas', 'MasterController::savePrioritasKriteria');

    // --- Sub Kriteria ---
    $routes->get('sub/(:num)', 'MasterController::subKriteria/$1');
    $routes->post('sub/store', 'MasterController::storeSub');
    $routes->get('sub/delete/(:num)', 'MasterController::deleteSub/$1');
    
    // Hitung Bobot Sub
    $routes->get('sub/prioritas/(:num)', 'MasterController::prioritasSub/$1'); 
    $routes->post('sub/save-prioritas', 'MasterController::savePrioritasSub');

    // --- Supplier ---
    $routes->get('supplier', 'MasterController::supplier');
    $routes->post('supplier/store', 'MasterController::storeSupplier');
    $routes->get('supplier/delete/(:num)', 'MasterController::deleteSupplier/$1');
});


// =========================================================================
// 5. GROUP: JENIS BAHAN & PENILAIAN KINERJA SUPPLIER
// URL: localhost:8080/jenis-bahan
// =========================================================================
$routes->group('jenis-bahan', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'JenisBahanController::index');
    $routes->post('store', 'JenisBahanController::store');
    $routes->get('delete/(:num)', 'JenisBahanController::delete/$1');

    // Setup Nilai Kinerja Supplier (Skor Lokal)
    $routes->get('setup/(:num)', 'JenisBahanController::setup/$1'); 
    $routes->get('setup-supplier/(:num)/(:num)', 'JenisBahanController::setupSupplier/$1/$2'); 
    $routes->post('save-supplier', 'JenisBahanController::saveSupplier');
});