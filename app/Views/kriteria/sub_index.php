<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('kriteria') ?>" class="text-gray-500 hover:text-indigo-600 font-bold">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Kriteria Utama
    </a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2">
        Sub Kriteria: <span class="text-indigo-600"><?= $parent['nama'] ?> (<?= $parent['kode'] ?>)</span>
    </h1>
    <p class="text-gray-600">Bobot Global Parent: <?= $parent['bobot_global'] ?></p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-lg shadow h-fit">
        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Tambah Sub Kriteria</h3>
        <form action="<?= base_url('kriteria/sub/store') ?>" method="post">
            <input type="hidden" name="id_kriteria" value="<?= $parent['id_kriteria'] ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Kode Sub (C1.1, dst)</label>
                <input type="text" name="kode" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Sub Kriteria</label>
                <input type="text" name="nama" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Simpan
            </button>
        </form>
    </div>

    <div class="md:col-span-2">
        <div class="flex justify-end mb-4">
            <a href="<?= base_url('kriteria/sub/prioritas/' . $parent['id_kriteria']) ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded shadow">
                <i class="fa-solid fa-scale-balanced mr-2"></i> Hitung Bobot Sub Kriteria
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bobot Lokal</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bobot Global</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subs as $s) : ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold text-indigo-600">
                            <?= $s['kode'] ?>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <?= $s['nama'] ?>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <?= number_format($s['bobot_lokal'], 3) ?>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold text-green-600">
                            <?= number_format($s['bobot_global'], 3) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>