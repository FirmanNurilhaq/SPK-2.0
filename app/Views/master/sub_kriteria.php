<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <a href="<?= base_url('master/kriteria') ?>" class="text-gray-500 hover:text-indigo-600 font-bold"><i class="fa-solid fa-arrow-left"></i> Kembali ke Kriteria</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2">Sub Kriteria: <span class="text-indigo-600"><?= $parent['nama'] ?></span></h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-lg shadow h-fit">
        <h3 class="text-lg font-bold mb-4 border-b pb-2">Tambah Sub Kriteria</h3>
        <form action="<?= base_url('master/sub/store') ?>" method="post">
            <input type="hidden" name="id_kriteria" value="<?= $parent['id_kriteria'] ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Kode (C1.1, C1.2...)</label>
                <input type="text" name="kode" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Sub Kriteria</label>
                <input type="text" name="nama" class="w-full border rounded px-3 py-2" required>
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 rounded hover:bg-indigo-700">Simpan</button>
        </form>
    </div>

    <div class="md:col-span-2 bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Nama Sub</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subs as $s) : ?>
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold text-indigo-600"><?= $s['kode'] ?></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= $s['nama'] ?></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                        <a href="<?= base_url('master/sub/delete/' . $s['id_sub_kriteria']) ?>" onclick="return confirm('Hapus?')" class="text-red-600 hover:text-red-900"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>