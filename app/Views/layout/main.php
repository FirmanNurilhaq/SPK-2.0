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
                        
                        <div class="relative group">
                            <button class="py-4 px-2 text-gray-500 font-semibold hover:text-indigo-500 transition duration-300 flex items-center">
                                Master Data <i class="fa-solid fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-0 w-48 z-10 border">
                                <a href="<?= base_url('master/kriteria') ?>" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50">Data Kriteria</a>
                                <a href="<?= base_url('master/supplier') ?>" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50">Data Supplier</a>
                            </div>
                        </div>

                        <a href="<?= base_url('jenis-bahan') ?>" 
                           class="py-4 px-2 text-gray-500 font-semibold hover:text-indigo-500 transition duration-300 <?= ($uri->getSegment(1) == 'jenis-bahan') ? 'text-indigo-500 border-b-4 border-indigo-500' : '' ?>">
                           Jenis Bahan (AHP)
                        </a>

                        <a href="<?= base_url('pemesanan') ?>" 
                           class="py-4 px-2 text-gray-500 font-semibold hover:text-indigo-500 transition duration-300 <?= ($uri->getSegment(1) == 'pemesanan') ? 'text-indigo-500 border-b-4 border-indigo-500' : '' ?>">
                           Pemesanan
                        </a>
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