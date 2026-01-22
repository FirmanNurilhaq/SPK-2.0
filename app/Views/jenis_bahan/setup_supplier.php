<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Penilaian Supplier</h2>
            <p class="text-gray-600 text-sm">Berdasarkan Sub Kriteria: <strong class="text-indigo-600"><?= $sub['nama'] ?></strong> (Konteks: <?= $bahan['nama_bahan'] ?>)</p>
        </div>
        <a href="<?= base_url('jenis-bahan/setup/' . $bahan['id_jenis_bahan']) ?>" class="text-gray-500 hover:text-gray-800 font-bold">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="<?= base_url('jenis-bahan/save-supplier') ?>" method="post">
        <input type="hidden" name="id_jenis_bahan" value="<?= $bahan['id_jenis_bahan'] ?>">
        <input type="hidden" name="id_sub_kriteria" value="<?= $sub['id_sub_kriteria'] ?>">

        <?php 
        $jumlah = count($suppliers);
        
        if ($jumlah < 2) : 
        ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Error</p>
                <p>Minimal harus ada 2 supplier untuk melakukan perbandingan. Silakan tambah data supplier di menu Master Data.</p>
            </div>
        <?php 
        else :
            // Loop kombinasi pasangan supplier
            for ($i = 0; $i < $jumlah; $i++) :
                for ($j = $i + 1; $j < $jumlah; $j++) :
                    $id_baris = $suppliers[$i]['id_supplier'];
                    $id_kolom = $suppliers[$j]['id_supplier'];
        ?>

        <div class="bg-white p-3 rounded shadow mb-3 border border-gray-200 hover:bg-gray-50 transition duration-150">
            <div class="flex items-center justify-between space-x-4">
                <div class="w-1/3 text-right font-semibold text-gray-700 text-sm">
                    <?= $suppliers[$i]['nama'] ?>
                </div>

                <div class="w-1/3">
                    <select name="nilai[<?= $id_baris ?>][<?= $id_kolom ?>]" class="w-full border border-gray-300 rounded text-center text-sm py-2 focus:ring-2 focus:ring-indigo-500">
                        <option value="1">1 - Sama Bagus</option>
                        <option value="3">3 - Kiri Lebih Bagus</option>
                        <option value="5">5 - Kiri Jauh Lebih Bagus</option>
                        <option value="7">7 - Kiri Sangat Lebih Bagus</option>
                        <option value="9">9 - Kiri Mutlak Bagus</option>
                        <option disabled>---</option>
                        <option value="0.3333">1/3 - Kanan Lebih Bagus</option>
                        <option value="0.2">1/5 - Kanan Jauh Lebih Bagus</option>
                        <option value="0.1428">1/7 - Kanan Sangat Lebih Bagus</option>
                        <option value="0.1111">1/9 - Kanan Mutlak Bagus</option>
                    </select>
                </div>

                <div class="w-1/3 text-left font-semibold text-gray-700 text-sm">
                    <?= $suppliers[$j]['nama'] ?>
                </div>
            </div>
        </div>

        <?php 
                endfor;
            endfor; 
        ?>

        <div class="mt-8 text-center pb-10">
            <button type="submit" class="bg-indigo-600 text-white font-bold py-3 px-10 rounded-full shadow-lg hover:bg-indigo-700 transform hover:-translate-y-1 transition">
                <i class="fa-solid fa-save mr-2"></i> Simpan Penilaian
            </button>
        </div>
        
        <?php endif; ?>
    </form>
</div>
<?= $this->endSection() ?>