<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <div class="flex justify-between items-center mb-4 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Pembobotan Sub Kriteria: <?= $parent['nama'] ?></h2>
        <a href="<?= base_url('kriteria/detail/' . $parent['id_kriteria']) ?>" class="text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="<?= base_url('kriteria/sub/update-matrix') ?>" method="post">
        <input type="hidden" name="id_kriteria_parent" value="<?= $parent['id_kriteria'] ?>">
        
        <?php 
        $jumlah = count($subs);
        for ($i = 0; $i < $jumlah; $i++) :
            for ($j = $i + 1; $j < $jumlah; $j++) :
                $id_baris = $subs[$i]['id_sub_kriteria'];
                $id_kolom = $subs[$j]['id_sub_kriteria'];
                $val = isset($matrix[$id_baris][$id_kolom]) ? $matrix[$id_baris][$id_kolom] : 1;
        ?>
        
        <div class="flex items-center justify-between bg-gray-50 p-3 rounded mb-2 border hover:bg-gray-100 transition">
            <div class="w-1/4 text-right font-semibold text-gray-700">
                <?= $subs[$i]['nama'] ?>
            </div>
            <div class="w-2/4 px-4 flex justify-center">
                <select name="nilai[<?= $id_baris ?>][<?= $id_kolom ?>]" class="w-full border border-gray-300 rounded px-2 py-1 text-center bg-white">
                    <option value="1" <?= $val == 1 ? 'selected' : '' ?>>1 - Sama</option>
                    <option value="3" <?= $val == 3 ? 'selected' : '' ?>>3 - Sedikit Lebih</option>
                    <option value="5" <?= $val == 5 ? 'selected' : '' ?>>5 - Lebih</option>
                    <option value="7" <?= $val == 7 ? 'selected' : '' ?>>7 - Sangat Lebih</option>
                    <option value="9" <?= $val == 9 ? 'selected' : '' ?>>9 - Mutlak</option>
                    <option disabled>--- Kebalikan ---</option>
                    <option value="0.3333" <?= abs($val - 0.3333) < 0.001 ? 'selected' : '' ?>>1/3 - Kanan Lebih</option>
                    <option value="0.2"    <?= abs($val - 0.2) < 0.001 ? 'selected' : '' ?>>1/5 - Kanan Lebih</option>
                    <option value="0.1428" <?= abs($val - 0.1428) < 0.001 ? 'selected' : '' ?>>1/7 - Kanan Lebih</option>
                    <option value="0.1111" <?= abs($val - 0.1111) < 0.001 ? 'selected' : '' ?>>1/9 - Kanan Mutlak</option>
                </select>
            </div>
            <div class="w-1/4 text-left font-semibold text-gray-700">
                <?= $subs[$j]['nama'] ?>
            </div>
        </div>

        <?php 
            endfor;
        endfor; 
        ?>

        <div class="mt-6 text-center">
            <button type="submit" class="bg-indigo-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg">
                Hitung Bobot
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>