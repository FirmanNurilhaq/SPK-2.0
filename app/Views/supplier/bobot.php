<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4 border-b pb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Bandingkan Supplier</h2>
            <p class="text-gray-600">Berdasarkan Sub Kriteria: <strong class="text-indigo-600"><?= $sub['nama'] ?></strong></p>
        </div>
        <a href="<?= base_url('supplier') ?>" class="text-gray-500 hover:text-gray-700"><i class="fa-solid fa-arrow-left"></i> Selesai</a>
    </div>

    <form action="<?= base_url('supplier/update-matrix') ?>" method="post">
        <input type="hidden" name="id_sub_kriteria" value="<?= $sub['id_sub_kriteria'] ?>">
        
        <?php 
        $jumlah = count($suppliers);
        for ($i = 0; $i < $jumlah; $i++) :
            for ($j = $i + 1; $j < $jumlah; $j++) :
                $id_baris = $suppliers[$i]['id_supplier'];
                $id_kolom = $suppliers[$j]['id_supplier'];
                $val = isset($matrix[$id_baris][$id_kolom]) ? $matrix[$id_baris][$id_kolom] : 1;
        ?>
        <div class="flex items-center justify-between bg-gray-50 p-3 rounded mb-2 border">
            <div class="w-1/4 text-right font-semibold text-gray-700"><?= $suppliers[$i]['nama'] ?></div>
            <div class="w-2/4 px-4 flex justify-center">
                <select name="nilai[<?= $id_baris ?>][<?= $id_kolom ?>]" class="w-full border rounded text-center">
                    <option value="1" <?= $val == 1 ? 'selected' : '' ?>>1 - Sama</option>
                    <option value="3" <?= $val == 3 ? 'selected' : '' ?>>3 - Kiri Lebih Baik</option>
                    <option value="5" <?= $val == 5 ? 'selected' : '' ?>>5 - Kiri Jauh Lebih Baik</option>
                    <option value="7" <?= $val == 7 ? 'selected' : '' ?>>7 - Kiri Sangat Lebih Baik</option>
                    <option value="9" <?= $val == 9 ? 'selected' : '' ?>>9 - Kiri Mutlak</option>
                    <option disabled>--- Kebalikan ---</option>
                    <option value="0.3333" <?= abs($val - 0.3333) < 0.001 ? 'selected' : '' ?>>1/3 - Kanan Lebih Baik</option>
                    <option value="0.2"    <?= abs($val - 0.2) < 0.001 ? 'selected' : '' ?>>1/5 - Kanan Jauh Lebih Baik</option>
                    <option value="0.1428" <?= abs($val - 0.1428) < 0.001 ? 'selected' : '' ?>>1/7 - Kanan Sangat Lebih Baik</option>
                    <option value="0.1111" <?= abs($val - 0.1111) < 0.001 ? 'selected' : '' ?>>1/9 - Kanan Mutlak</option>
                </select>
            </div>
            <div class="w-1/4 text-left font-semibold text-gray-700"><?= $suppliers[$j]['nama'] ?></div>
        </div>
        <?php endfor; endfor; ?>
        
        <div class="mt-6 text-center">
            <button type="submit" class="bg-indigo-600 text-white font-bold py-3 px-8 rounded">Simpan & Hitung Skor</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>