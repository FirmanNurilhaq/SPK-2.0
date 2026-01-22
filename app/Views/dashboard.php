<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-indigo-500">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang, Bagian Pengadaan!</h2>
        <p class="text-gray-600 mb-4">Sistem ini membantu Anda memilih supplier bahan rajut terbaik menggunakan metode AHP (Analytic Hierarchy Process).</p>
        <div class="mt-4">
            <a href="<?= base_url('pemesanan/create') ?>" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                <i class="fa-solid fa-plus mr-1"></i> Buat Pesanan Baru
            </a>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Status Data</h3>
        <ul class="space-y-2">
            <li class="flex justify-between items-center border-b pb-2">
                <span>Kriteria Terdaftar</span>
                <span class="font-bold text-indigo-600">Cek Menu Kriteria</span>
            </li>
            <li class="flex justify-between items-center border-b pb-2">
                <span>Supplier Terdaftar</span>
                <span class="font-bold text-indigo-600">Cek Menu Supplier</span>
            </li>
        </ul>
    </div>
</div>

<?= $this->endSection() ?>