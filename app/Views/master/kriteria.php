<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Master Data Kriteria</h1>
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        + Tambah Kriteria
    </button>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kriteria</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kriteria as $k) : ?>
            <tr>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold text-indigo-600">
                    <?= $k['kode'] ?>
                </td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                    <?= $k['nama'] ?>
                </td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                    <a href="<?= base_url('master/sub/' . $k['id_kriteria']) ?>" class="bg-blue-100 text-blue-800 px-3 py-1 rounded text-xs font-bold hover:bg-blue-200 mr-2">
                        <i class="fa-solid fa-list-tree mr-1"></i> Sub Kriteria
                    </a>
                    <a href="<?= base_url('master/kriteria/delete/' . $k['id_kriteria']) ?>" onclick="return confirm('Hapus kriteria ini?')" class="text-red-600 hover:text-red-900">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="modalAdd" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4">Tambah Kriteria</h3>
        <form action="<?= base_url('master/kriteria/store') ?>" method="post">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Kode (C1, C2...)</label>
                <input type="text" name="kode" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kriteria</label>
                <input type="text" name="nama" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="mr-2 text-gray-500 hover:text-gray-700">Batal</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>