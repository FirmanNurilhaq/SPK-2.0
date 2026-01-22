<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <div class="flex justify-between items-center mb-4 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Pembobotan Antar Kriteria (AHP)</h2>
        <a href="<?= base_url('kriteria') ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 text-sm">
        <p class="font-bold">Petunjuk:</p>
        <p>Bandingkan tingkat kepentingan antara Kriteria Baris (Kiri) terhadap Kriteria Kolom (Kanan).</p>
        <p>Skala 1 = Sama Penting, 9 = Mutlak Lebih Penting.</p>
    </div>

    <form action="<?= base_url('kriteria/update-matrix') ?>" method="post">
        
        <?php 
        $jumlah = count($kriteria);
        // Loop untuk membuat kombinasi pasangan
        for ($i = 0; $i < $jumlah; $i++) :
            for ($j = $i + 1; $j < $jumlah; $j++) :
                $id_baris = $kriteria[$i]['id_kriteria'];
                $id_kolom = $kriteria[$j]['id_kriteria'];
                
                // Ambil nilai lama jika ada
                $val = isset($matrix[$id_baris][$id_kolom]) ? $matrix[$id_baris][$id_kolom] : 1;
        ?>
        
        <div class="flex items-center justify-between bg-gray-50 p-3 rounded mb-2 border hover:bg-gray-100 transition">
            <div class="w-1/4 text-right font-semibold text-gray-700">
                <?= $kriteria[$i]['nama'] ?> (<?= $kriteria[$i]['kode'] ?>)
            </div>

            <div class="w-2/4 px-4 flex justify-center">
                <select name="nilai[<?= $id_baris ?>][<?= $id_kolom ?>]" class="w-full border border-gray-300 rounded px-2 py-1 text-center bg-white focus:ring-2 focus:ring-indigo-400">
                    <option value="1" <?= $val == 1 ? 'selected' : '' ?>>1 - Sama pentingnya</option>
                    <option value="3" <?= $val == 3 ? 'selected' : '' ?>>3 - Sedikit lebih penting</option>
                    <option value="5" <?= $val == 5 ? 'selected' : '' ?>>5 - Lebih penting</option>
                    <option value="7" <?= $val == 7 ? 'selected' : '' ?>>7 - Sangat lebih penting</option>
                    <option value="9" <?= $val == 9 ? 'selected' : '' ?>>9 - Mutlak lebih penting</option>
                    
                    <option disabled>--- Kebalikan (Kanan Lebih Penting) ---</option>
                    <option value="0.3333" <?= abs($val - 0.3333) < 0.001 ? 'selected' : '' ?>>1/3 - Kanan sedikit lebih penting</option>
                    <option value="0.2"    <?= abs($val - 0.2) < 0.001 ? 'selected' : '' ?>>1/5 - Kanan lebih penting</option>
                    <option value="0.1428" <?= abs($val - 0.1428) < 0.001 ? 'selected' : '' ?>>1/7 - Kanan sangat lebih penting</option>
                    <option value="0.1111" <?= abs($val - 0.1111) < 0.001 ? 'selected' : '' ?>>1/9 - Kanan mutlak lebih penting</option>
                </select>
            </div>

            <div class="w-1/4 text-left font-semibold text-gray-700">
                <?= $kriteria[$j]['nama'] ?> (<?= $kriteria[$j]['kode'] ?>)
            </div>
        </div>

        <?php 
            endfor;
        endfor; 
        ?>

        <div class="mt-6 text-center">
            <button type="submit" class="bg-indigo-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:bg-indigo-700 transform transition hover:-translate-y-1">
                <i class="fa-solid fa-calculator mr-2"></i> Simpan & Hitung Bobot
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>