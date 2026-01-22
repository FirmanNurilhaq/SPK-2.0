<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Dashboard Utama
$routes->get('/', 'Home::index');

// =================================================
// 1. MODULE KRITERIA & SUB KRITERIA
// =================================================
$routes->group('kriteria', function($routes) {
    // --- Kriteria Utama (Parent) ---
    $routes->get('/', 'KriteriaController::index');                  // List Kriteria
    $routes->post('store', 'KriteriaController::store');             // Simpan Kriteria Baru
    $routes->get('delete/(:num)', 'KriteriaController::delete/$1');  // Hapus Kriteria
    
    // Pembobotan Kriteria Utama (Pairwise Comparison)
    $routes->get('prioritas', 'KriteriaController::prioritas');      // View Matriks
    $routes->post('update-matrix', 'KriteriaController::updateMatrix'); // Proses Hitung AHP
    
    // --- Sub Kriteria (Child) ---
    $routes->get('detail/(:num)', 'KriteriaController::detail/$1');  // Lihat List Sub Kriteria dari Parent tertentu
    $routes->post('sub/store', 'KriteriaController::storeSub');      // Simpan Sub Kriteria Baru
    
    // Pembobotan Sub Kriteria (Pairwise Comparison)
    $routes->get('sub/prioritas/(:num)', 'KriteriaController::prioritasSub/$1'); // View Matriks Sub
    $routes->post('sub/update-matrix', 'KriteriaController::updateMatrixSub');   // Proses Hitung AHP Sub
});

// =================================================
// 2. MODULE SUPPLIER
// =================================================
$routes->group('supplier', function($routes) {
    $routes->get('/', 'SupplierController::index');                  // List Supplier
    $routes->post('store', 'SupplierController::store');             // Simpan Supplier Baru
    $routes->get('delete/(:num)', 'SupplierController::delete/$1');  // Hapus Supplier
    
    // Pembobotan Supplier (Bandingkan Supplier A vs B berdasarkan Sub Kriteria X)
    $routes->get('bobot/(:num)', 'SupplierController::bobot/$1');    // View Matriks Supplier (parameter: id_sub_kriteria)
    $routes->post('update-matrix', 'SupplierController::updateMatrix'); // Proses Hitung Skor Supplier
});

// =================================================
// 3. MODULE PEMESANAN (TRANSAKSI)
// =================================================
$routes->group('pemesanan', function($routes) {
    $routes->get('/', 'PemesananController::index');                 // List History Pesanan
    $routes->get('create', 'PemesananController::create');           // Form Order & Leaderboard AHP Live
    $routes->post('store', 'PemesananController::store');            // Proses Simpan Order & Snapshot History
    $routes->get('detail/(:num)', 'PemesananController::detail/$1'); // Lihat Detail & Snapshot Ranking saat itu
});