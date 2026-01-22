<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Master Data Supplier</h1>
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        + Tambah Supplier
    </button>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Nama Perusahaan</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Kontak</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($supplier as $s) : ?>
            <tr>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold text-indigo-600"><?= $s['kode'] ?></td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= $s['nama'] ?></td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= $s['kontak'] ?></td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                    <a href="<?= base_url('master/supplier/delete/' . $s['id_supplier']) ?>" onclick="return confirm('Hapus supplier ini?')" class="text-red-600 hover:text-red-900"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="modalAdd" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4">Tambah Supplier</h3>
        <form action="<?= base_url('master/supplier/store') ?>" method="post">
            <div class="mb-3">
                <label class="block text-sm font-bold text-gray-700">Kode (S1, S2...)</label>
                <input type="text" name="kode" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-bold text-gray-700">Nama Perusahaan</label>
                <input type="text" name="nama" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-bold text-gray-700">Alamat</label>
                <textarea name="alamat" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-bold text-gray-700">Kontak</label>
                <input type="text" name="kontak" class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="mr-2 text-gray-500 hover:text-gray-700">Batal</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>