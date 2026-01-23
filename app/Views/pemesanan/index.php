<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6 border-b flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Riwayat Pemesanan</h2>
        <a href="<?= base_url('pemesanan/create') ?>" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-indigo-700">
            + Pesanan Baru
        </a>
    </div>
    
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail Barang</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier Dipilih</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach($history as $h): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?= date('d M Y H:i', strtotime($h['tanggal'])) ?>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900"><?= $h['jumlah_lusin'] ?> Lusin</div>
                    <div class="text-sm text-gray-500"><?= $h['nama_bahan'] ?></div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 font-semibold">
                    <?= $h['nama_supplier'] ?>
                </td>
                <td class="px-6 py-4 text-center">
                    <a href="<?= base_url('pemesanan/detail/'.$h['id_pesanan']) ?>" class="text-indigo-600 hover:text-indigo-900 font-bold text-sm">
                        Lihat Hasil AHP
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>