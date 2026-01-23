<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

// ==========================================================
// 1. MASTER DATA & BOBOT GLOBAL
// CRUD Data + Pembobotan Kriteria & Sub (Sekali Input)
// ==========================================================
$routes->group('master', function($routes) {
    // --- Kriteria ---
    $routes->get('kriteria', 'MasterController::kriteria');
    $routes->post('kriteria/store', 'MasterController::storeKriteria');
    $routes->get('kriteria/delete/(:num)', 'MasterController::deleteKriteria/$1');
    
    // Fitur Baru: Hitung Bobot Global Kriteria
    $routes->get('kriteria/prioritas', 'MasterController::prioritasKriteria'); 
    $routes->post('kriteria/save-prioritas', 'MasterController::savePrioritasKriteria');

    // --- Sub Kriteria ---
    $routes->get('sub/(:num)', 'MasterController::subKriteria/$1');
    $routes->post('sub/store', 'MasterController::storeSub');
    $routes->get('sub/delete/(:num)', 'MasterController::deleteSub/$1');

    // Fitur Baru: Hitung Bobot Global Sub Kriteria
    $routes->get('sub/prioritas/(:num)', 'MasterController::prioritasSub/$1');
    $routes->post('sub/save-prioritas', 'MasterController::savePrioritasSub');

    // --- Supplier ---
    $routes->get('supplier', 'MasterController::supplier');
    $routes->post('supplier/store', 'MasterController::storeSupplier');
    $routes->get('supplier/delete/(:num)', 'MasterController::deleteSupplier/$1');
});

// ==========================================================
// 2. JENIS BAHAN & PENILAIAN SUPPLIER (DINAMIS)
// User memilih bahan, lalu menilai supplier KHUSUS bahan itu.
// ==========================================================
$routes->group('jenis-bahan', function($routes) {
    $routes->get('/', 'JenisBahanController::index');
    $routes->post('store', 'JenisBahanController::store');
    $routes->get('delete/(:num)', 'JenisBahanController::delete/$1');

    // Dashboard Setup untuk 1 Bahan
    $routes->get('setup/(:num)', 'JenisBahanController::setup/$1'); 

    // HANYA ADA SETUP SUPPLIER (Kriteria & Sub ikut Master)
    $routes->get('setup-supplier/(:num)/(:num)', 'JenisBahanController::setupSupplier/$1/$2'); // id_bahan, id_sub
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
    $routes->get('get-leaderboard/(:num)', 'PemesananController::getLeaderboard/$1');
    $routes->get('debug/(:num)', 'PemesananController::debug/$1'); // <--- Tambah ini
});