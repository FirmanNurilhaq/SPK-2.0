<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Data Pembeli</h1>
        <p class="text-gray-600 text-sm">Kelola data pelanggan untuk mempercepat proses pemesanan.</p>
    </div>
    
    <div class="flex space-x-2">
        <a href="<?= base_url('pemesanan') ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 font-bold">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
        <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 font-bold shadow">
            <i class="fa-solid fa-user-plus mr-2"></i> Tambah Pembeli
        </button>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
    <table class="min-w-full leading-normal">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Pembeli</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alamat</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kontak / HP</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if(empty($pembeli)): ?>
                <tr>
                    <td colspan="4" class="px-5 py-8 text-center text-gray-500 italic">
                        Belum ada data pembeli. Silakan tambah data baru.
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($pembeli as $p) : ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4 text-sm text-gray-500 font-mono">
                        <?= $no++ ?>
                    </td>
                    <td class="px-5 py-4 text-sm font-bold text-gray-800">
                        <?= $p['nama_pembeli'] ?>
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-600">
                        <?= $p['alamat'] ?>
                    </td>
                    <td class="px-5 py-4 text-sm text-indigo-600 font-semibold">
                        <?= $p['kontak'] ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="modalAdd" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h3 class="text-lg font-bold text-gray-800">Tambah Pembeli Baru</h3>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        
        <form action="<?= base_url('pemesanan/pembeli/store') ?>" method="post">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Pembeli</label>
                <input type="text" name="nama" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required placeholder="Contoh: CV. Berkah Abadi">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Lengkap</label>
                <textarea name="alamat" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="3" required placeholder="Jalan Raya No. 123..."></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Kontak (HP/Telp)</label>
                <input type="text" name="kontak" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required placeholder="0812xxxx">
            </div>
            
            <div class="flex justify-end pt-2">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="mr-2 text-gray-500 hover:text-gray-700 font-bold px-4 py-2 rounded">Batal</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 font-bold shadow">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Close modal when clicking outside
    window.onclick = function(event) {
        let modal = document.getElementById('modalAdd');
        if (event.target == modal) {
            modal.classList.add('hidden');
        }
    }
</script>

<?= $this->endSection() ?>