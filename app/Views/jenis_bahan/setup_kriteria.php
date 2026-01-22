<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Bobot Kriteria: <span class="text-indigo-600"><?= $bahan['nama_bahan'] ?></span></h2>
            <p class="text-gray-600 text-sm">Tentukan prioritas kriteria khusus untuk bahan ini.</p>
        </div>
        <a href="<?= base_url('jenis-bahan/setup/' . $bahan['id_jenis_bahan']) ?>" class="text-gray-500 hover:text-gray-800 font-bold">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="<?= base_url('jenis-bahan/save-kriteria') ?>" method="post">
        <input type="hidden" name="id_jenis_bahan" value="<?= $bahan['id_jenis_bahan'] ?>">

        <?php 
        $jumlah = count($kriteria);
        $no = 1;
        // Loop kombinasi pasangan kriteria
        for ($i = 0; $i < $jumlah; $i++) :
            for ($j = $i + 1; $j < $jumlah; $j++) :
                $id_baris = $kriteria[$i]['id_kriteria'];
                $id_kolom = $kriteria[$j]['id_kriteria'];
        ?>

        <div class="bg-white p-4 rounded-lg shadow mb-4 border border-gray-200">
            <div class="flex items-center justify-between text-center mb-2">
                <div class="w-1/3 font-bold text-gray-800"><?= $kriteria[$i]['nama'] ?></div>
                <div class="w-1/3 text-xs text-gray-400">PERTANDINGAN #<?= $no++ ?></div>
                <div class="w-1/3 font-bold text-gray-800"><?= $kriteria[$j]['nama'] ?></div>
            </div>

            <div class="flex justify-center items-center space-x-4">
                <select name="nilai[<?= $id_baris ?>][<?= $id_kolom ?>]" class="border-2 border-indigo-200 rounded px-4 py-2 bg-indigo-50 font-bold text-indigo-700 focus:outline-none focus:border-indigo-500 text-center w-full">
                    <option value="1">1 - Sama Penting</option>
                    <option value="3">3 - Kiri Sedikit Lebih Penting</option>
                    <option value="5">5 - Kiri Lebih Penting</option>
                    <option value="7">7 - Kiri Sangat Penting</option>
                    <option value="9">9 - Kiri Mutlak Penting</option>
                    <option disabled>----------------</option>
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

        <?php if($jumlah < 2): ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p>Minimal harus ada 2 kriteria untuk melakukan pembobotan.</p>
            </div>
        <?php else: ?>
            <div class="mt-6 text-center">
                <button type="submit" class="bg-indigo-600 text-white font-bold py-3 px-8 rounded-lg shadow hover:bg-indigo-700">
                    Simpan & Hitung Bobot Kriteria
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>
<?= $this->endSection() ?>