<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Rajut & SPK AHP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>body { font-family: 'Segoe UI', sans-serif; background-color: #f3f4f6; }</style>
</head>
<body class="flex flex-col min-h-screen">
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 flex justify-between">
            <div class="flex space-x-7">
                <a href="#" class="flex items-center py-4 px-2">
                    <i class="fa-solid fa-shirt text-indigo-600 text-2xl mr-2"></i>
                    <span class="font-bold text-gray-500 text-lg">Rajut AHP</span>
                </a>
                <div class="hidden md:flex items-center space-x-1">
                    
                    <?php if(session()->get('role') == 'pemesanan'): ?>
                        <a href="<?= base_url('pemesanan') ?>" class="py-4 px-2 font-semibold hover:text-indigo-500 text-gray-500">Dashboard & History</a>
                        <a href="<?= base_url('pemesanan/create') ?>" class="py-4 px-2 font-semibold hover:text-indigo-500 text-gray-500">Input Order</a>
                        <a href="<?= base_url('pemesanan/pembeli') ?>" class="py-4 px-2 font-semibold hover:text-indigo-500 text-gray-500">Data Pembeli</a>
                    
                    <?php elseif(session()->get('role') == 'pengadaan'): ?>
                        <a href="<?= base_url('pengadaan') ?>" class="py-4 px-2 font-semibold hover:text-indigo-500 text-gray-500">Order Masuk (Pending)</a>
                        <a href="<?= base_url('master/kriteria') ?>" class="py-4 px-2 font-semibold hover:text-indigo-500 text-gray-500">Kriteria AHP</a>
                        <a href="<?= base_url('master/supplier') ?>" class="py-4 px-2 font-semibold hover:text-indigo-500 text-gray-500">Data Supplier</a>
                        <a href="<?= base_url('jenis-bahan') ?>" class="py-4 px-2 font-semibold hover:text-indigo-500 text-gray-500">Jenis Bahan & Penilaian</a>
                    <?php endif; ?>

                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-500">Hi, <b><?= session()->get('nama') ?></b></span>
                <a href="<?= base_url('logout') ?>" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">Logout</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-8">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?= $this->renderSection('content') ?>
    </main>
    
    <footer class="bg-gray-800 text-white py-4 mt-8 text-center">
        <p>&copy; <?= date('Y') ?> Sistem Rajut AHP</p>
    </footer>
</body>
</html>