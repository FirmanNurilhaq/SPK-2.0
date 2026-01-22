<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Rajut & SPK AHP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; }
        .active-nav { background-color: #1f2937; color: white; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div>
                        <a href="<?= base_url('/') ?>" class="flex items-center py-4 px-2">
                            <i class="fa-solid fa-shirt text-indigo-600 text-2xl mr-2"></i>
                            <span class="font-bold text-gray-500 text-lg">Rajut AHP</span>
                        </a>
                    </div>
                    <div class="hidden md:flex items-center space-x-1">
                        <?php $uri = service('uri'); ?>
                        
                        <a href="<?= base_url('/') ?>" 
                           class="py-4 px-2 text-gray-500 font-semibold hover:text-indigo-500 transition duration-300 <?= ($uri->getSegment(1) == '') ? 'text-indigo-500 border-b-4 border-indigo-500' : '' ?>">
                           Dashboard
                        </a>
                        
                        <a href="<?= base_url('kriteria') ?>" 
                           class="py-4 px-2 text-gray-500 font-semibold hover:text-indigo-500 transition duration-300 <?= ($uri->getSegment(1) == 'kriteria') ? 'text-indigo-500 border-b-4 border-indigo-500' : '' ?>">
                           Data Kriteria (Setup)
                        </a>

                        <a href="<?= base_url('supplier') ?>" 
                           class="py-4 px-2 text-gray-500 font-semibold hover:text-indigo-500 transition duration-300 <?= ($uri->getSegment(1) == 'supplier') ? 'text-indigo-500 border-b-4 border-indigo-500' : '' ?>">
                           Data Supplier (Setup)
                        </a>

                        <a href="<?= base_url('pemesanan') ?>" 
                           class="py-4 px-2 text-gray-500 font-semibold hover:text-indigo-500 transition duration-300 <?= ($uri->getSegment(1) == 'pemesanan') ? 'text-indigo-500 border-b-4 border-indigo-500' : '' ?>">
                           Pemesanan & History
                        </a>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-3 ">
                    <div class="py-2 px-2 font-medium text-gray-500 rounded hover:bg-gray-100 transition duration-300">
                        <i class="fa-solid fa-user-tie mr-1"></i> Bagian Pengadaan
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-8">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <footer class="bg-gray-800 text-white py-4 mt-8">
        <div class="container mx-auto text-center">
            <p>&copy; <?= date('Y') ?> Sistem Pendukung Keputusan Pemilihan Supplier Rajut.</p>
        </div>
    </footer>

</body>
</html>