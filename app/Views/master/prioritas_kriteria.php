<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Hitung Bobot Prioritas Kriteria</h2>
            <p class="text-gray-600 text-sm">Pembobotan ini berlaku secara <strong>Global</strong> untuk semua jenis bahan.</p>
        </div>
        <a href="<?= base_url('master/kriteria') ?>" class="text-gray-500 hover:text-gray-800 font-bold">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="<?= base_url('master/kriteria/save-prioritas') ?>" method="post">
        <?php 
        $jumlah = count($kriteria);
        
        if ($jumlah < 2) : 
        ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p>Minimal harus ada 2 kriteria untuk melakukan pembobotan.</p>
            </div>
        <?php 
        else :
            $no = 1;
            // Loop kombinasi pasangan kriteria
            for ($i = 0; $i < $jumlah; $i++) :
                for ($j = $i + 1; $j < $jumlah; $j++) :
                    $id_baris = $kriteria[$i]['id_kriteria'];
                    $id_kolom = $kriteria[$j]['id_kriteria'];
        ?>

        <div class="bg-white p-4 rounded-lg shadow mb-4 border border-gray-200">
            <div class="flex items-center justify-between text-center mb-2">
                <div class="w-1/3 font-bold text-gray-800 text-lg"><?= $kriteria[$i]['nama'] ?></div>
                <div class="w-1/3 text-xs text-gray-400 font-bold tracking-widest">PERTANDINGAN #<?= $no++ ?></div>
                <div class="w-1/3 font-bold text-gray-800 text-lg"><?= $kriteria[$j]['nama'] ?></div>
            </div>

            <div class="flex justify-center items-center">
                <select name="nilai[<?= $id_baris ?>][<?= $id_kolom ?>]" class="w-full md:w-1/2 border-2 border-indigo-100 rounded px-4 py-2 bg-indigo-50 font-bold text-indigo-700 focus:outline-none focus:border-indigo-500 text-center cursor-pointer">
                    <option value="1">1 - Sama Penting</option>
                    <option value="3">3 - Kiri Sedikit Lebih Penting</option>
                    <option value="5">5 - Kiri Lebih Penting</option>
                    <option value="7">7 - Kiri Sangat Penting</option>
                    <option value="9">9 - Kiri Mutlak Penting</option>
                    <option disabled class="bg-gray-200">----------------</option>
                    <option value="0.3333">1/3 - Kanan Sedikit Lebih Penting</option>
                    <option value="0.2">1/5 - Kanan Lebih Penting</option>
                    <option value="0.1428">1/7 - Kanan Sangat Penting</option>
                    <option value="0.1111">1/9 - Kanan Mutlak Penting</option>
                </select>
            </div>
        </div>

        <?php 
                endfor;
            endfor; 
        ?>

        <div class="mt-8 text-center pb-10">
            <button type="submit" class="bg-yellow-500 text-white font-bold py-3 px-10 rounded-full shadow-lg hover:bg-yellow-600 transform hover:-translate-y-1 transition duration-200 flex items-center justify-center mx-auto">
                <i class="fa-solid fa-calculator mr-2"></i> Simpan & Hitung Bobot Global
            </button>
        </div>
        
        <?php endif; ?>
    </form>
</div>
<?= $this->endSection() ?>