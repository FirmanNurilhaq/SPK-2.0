<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

// ==========================================================
// 1. MASTER DATA (Kriteria, Sub Kriteria, Supplier)
// Hanya input nama/kode, tanpa bobot.
// ==========================================================
$routes->group('master', function($routes) {
    // Kriteria
    $routes->get('kriteria', 'MasterController::kriteria');
    $routes->post('kriteria/store', 'MasterController::storeKriteria');
    $routes->get('kriteria/delete/(:num)', 'MasterController::deleteKriteria/$1');
    
    // Sub Kriteria
    $routes->get('sub/(:num)', 'MasterController::subKriteria/$1'); // parameter id_kriteria
    $routes->post('sub/store', 'MasterController::storeSub');
    $routes->get('sub/delete/(:num)', 'MasterController::deleteSub/$1');

    // Supplier
    $routes->get('supplier', 'MasterController::supplier');
    $routes->post('supplier/store', 'MasterController::storeSupplier');
    $routes->get('supplier/delete/(:num)', 'MasterController::deleteSupplier/$1');
});

// ==========================================================
// 2. JENIS BAHAN & SETUP AHP (INTI SISTEM)
// Di sini user input Bahan & Melakukan Pembobotan per Bahan
// ==========================================================
$routes->group('jenis-bahan', function($routes) {
    $routes->get('/', 'JenisBahanController::index');           // List Jenis Bahan
    $routes->post('store', 'JenisBahanController::store');      // Tambah Bahan Baru
    $routes->get('delete/(:num)', 'JenisBahanController::delete/$1');

    // --- MENU SETUP AHP PER BAHAN ---
    // Dashboard Setup untuk 1 Bahan Tertentu
    $routes->get('setup/(:num)', 'JenisBahanController::setup/$1'); 

    // A. Setup Bobot Kriteria (Khusus Bahan ini)
    $routes->get('setup-kriteria/(:num)', 'JenisBahanController::setupKriteria/$1');
    $routes->post('save-kriteria', 'JenisBahanController::saveKriteria');

    // B. Setup Bobot Sub Kriteria (Khusus Bahan ini)
    $routes->get('setup-sub/(:num)/(:num)', 'JenisBahanController::setupSub/$1/$2'); // id_bahan, id_kriteria_parent
    $routes->post('save-sub', 'JenisBahanController::saveSub');

    // C. Setup Nilai Supplier (Khusus Bahan ini)
    $routes->get('setup-supplier/(:num)/(:num)', 'JenisBahanController::setupSupplier/$1/$2'); // id_bahan, id_sub_kriteria
    $routes->post('save-supplier', 'JenisBahanController::saveSupplier');
});

// ==========================================================
// 3. PEMESANAN (TRANSAKSI)
// ==========================================================
$routes->group('pemesanan', function($routes) {
    $routes->get('/', 'PemesananController::index');
    $routes->get('create', 'PemesananController::create');
    $routes->post('store', 'PemesananController::store');
    $routes->get('detail/(:num)', 'PemesananController::detail/$1');

    // API untuk mengambil leaderboard saat dropdown bahan dipilih
    $routes->get('get-leaderboard/(:num)', 'PemesananController::getLeaderboard/$1'); // id_jenis_bahan
});