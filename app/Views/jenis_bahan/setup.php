<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('jenis-bahan') ?>" class="text-gray-500 font-bold"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    <h1 class="text-3xl font-bold text-gray-800 mt-2">Penilaian Supplier: <span class="text-indigo-600"><?= $bahan['nama_bahan'] ?></span></h1>
    <p class="text-gray-600">Silakan nilai performa supplier untuk setiap sub kriteria di bawah ini.</p>
</div>

<div class="bg-white p-6 rounded-lg shadow">
    <table class="min-w-full border">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">Kriteria</th>
                <th class="px-4 py-2 text-left">Sub Kriteria</th>
                <th class="px-4 py-2 text-left">Bobot Global (Master)</th>
                <th class="px-4 py-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach($sub as $s): ?>
            <tr>
                <td class="px-4 py-2 text-gray-500"><?= $s['nama_kriteria'] ?></td>
                <td class="px-4 py-2 font-bold"><?= $s['nama'] ?></td>
                <td class="px-4 py-2 text-gray-500 text-sm"><?= number_format($s['bobot_global'], 4) ?></td>
                <td class="px-4 py-2 text-center">
                    <a href="<?= base_url('jenis-bahan/setup-supplier/'.$bahan['id_jenis_bahan'].'/'.$s['id_sub_kriteria']) ?>" class="bg-indigo-600 text-white px-4 py-1 rounded text-sm hover:bg-indigo-700">
                        Nilai Supplier
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>