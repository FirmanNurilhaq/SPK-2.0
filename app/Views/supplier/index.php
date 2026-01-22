<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-lg shadow h-fit">
        <h3 class="text-lg font-bold mb-4 border-b pb-2">Tambah Supplier Baru</h3>
        <form action="<?= base_url('supplier/store') ?>" method="post">
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
            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 rounded hover:bg-indigo-700">Simpan Supplier</button>
        </form>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h3 class="text-lg font-bold mb-4">Daftar Supplier</h3>
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="py-2 px-4 text-left">Kode</th>
                        <th class="py-2 px-4 text-left">Nama</th>
                        <th class="py-2 px-4 text-left">Kontak</th>
                        <th class="py-2 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($supplier as $s): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4 font-bold text-indigo-600"><?= $s['kode'] ?></td>
                        <td class="py-2 px-4"><?= $s['nama'] ?></td>
                        <td class="py-2 px-4"><?= $s['kontak'] ?></td>
                        <td class="py-2 px-4 text-center">
                            <a href="<?= base_url('supplier/delete/'.$s['id_supplier']) ?>" onclick="return confirm('Hapus?')" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500">
            <h3 class="text-lg font-bold mb-2">Setup Penilaian Supplier</h3>
            <p class="text-sm text-gray-600 mb-4">Pilih Sub Kriteria untuk membandingkan kinerja antar supplier.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <?php foreach ($subs as $sub): ?>
                <a href="<?= base_url('supplier/bobot/'.$sub['id_sub_kriteria']) ?>" class="flex justify-between items-center bg-gray-50 border p-3 rounded hover:bg-blue-50 transition group">
                    <div>
                        <span class="text-xs text-gray-500"><?= $sub['nama_kriteria'] ?></span>
                        <div class="font-bold text-gray-800 group-hover:text-blue-600"><?= $sub['nama'] ?></div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>