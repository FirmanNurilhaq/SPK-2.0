<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('jenis-bahan') ?>" class="text-gray-500 font-bold"><i class="fa-solid fa-arrow-left"></i> Daftar Bahan</a>
    <h1 class="text-3xl font-bold text-gray-800 mt-2">Setup AHP: <span class="text-indigo-600"><?= $bahan['nama_bahan'] ?></span></h1>
    <p class="text-gray-600">Lakukan pembobotan secara berurutan.</p>
</div>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg shadow border-l-4 <?= $statusKriteria ? 'border-green-500' : 'border-red-500' ?>">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold">1. Bobot Kriteria</h3>
                <p class="text-sm text-gray-500">Bandingkan kepentingan antar kriteria utama untuk bahan ini.</p>
            </div>
            <a href="<?= base_url('jenis-bahan/setup-kriteria/' . $bahan['id_jenis_bahan']) ?>" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                <?= $statusKriteria ? 'Update Bobot' : 'Mulai Hitung' ?>
            </a>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-bold mb-4">2. Bobot Sub Kriteria & 3. Skor Supplier</h3>
        <p class="text-sm text-gray-500 mb-4">Klik pada setiap Sub Kriteria untuk mengatur bobot lokalnya dan menilai supplier.</p>
        
        <table class="min-w-full border">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Kriteria Parent</th>
                    <th class="px-4 py-2 text-left">Sub Kriteria</th>
                    <th class="px-4 py-2 text-center">Aksi Sub</th>
                    <th class="px-4 py-2 text-center">Aksi Supplier</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach($sub as $s): ?>
                <tr>
                    <td class="px-4 py-2 text-gray-500 text-sm"><?= $s['nama_kriteria'] ?></td>
                    <td class="px-4 py-2 font-bold"><?= $s['nama'] ?></td>
                    <td class="px-4 py-2 text-center">
                        <a href="<?= base_url('jenis-bahan/setup-sub/'.$bahan['id_jenis_bahan'].'/'.$s['id_kriteria']) ?>" class="text-blue-600 hover:underline text-xs">
                            Atur Bobot Sub
                        </a>
                    </td>
                    <td class="px-4 py-2 text-center">
                        <a href="<?= base_url('jenis-bahan/setup-supplier/'.$bahan['id_jenis_bahan'].'/'.$s['id_sub_kriteria']) ?>" class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded text-xs font-bold hover:bg-yellow-200">
                            Nilai Supplier
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>