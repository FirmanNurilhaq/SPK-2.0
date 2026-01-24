<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">Dashboard Pemesanan (History Order)</h2>
    <a href="<?= base_url('pemesanan/create') ?>" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-indigo-700 shadow">
        <i class="fa-solid fa-plus mr-2"></i> Input Order Baru
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded shadow border-l-4 border-indigo-500">
        <p class="text-gray-500 text-sm">Total Pesanan</p>
        <p class="text-2xl font-bold"><?= count($history) ?></p>
    </div>
    <div class="bg-white p-4 rounded shadow border-l-4 border-green-500">
        <p class="text-gray-500 text-sm">Pesanan Selesai</p>
        <p class="text-2xl font-bold text-green-600">
            <?= count(array_filter($history, function($h){ return $h['status'] == 'selesai'; })) ?>
        </p>
    </div>
    <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
        <p class="text-gray-500 text-sm">Menunggu (Pending)</p>
        <p class="text-2xl font-bold text-yellow-600">
            <?= count(array_filter($history, function($h){ return $h['status'] == 'pending'; })) ?>
        </p>
    </div>
</div>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="p-4 font-semibold text-gray-600 text-sm">Tanggal</th>
                <th class="p-4 font-semibold text-gray-600 text-sm">Pembeli</th>
                <th class="p-4 font-semibold text-gray-600 text-sm">Detail Barang</th>
                <th class="p-4 font-semibold text-gray-600 text-sm">Status</th>
                <th class="p-4 font-semibold text-gray-600 text-sm">Supplier Terpilih</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if(empty($history)): ?>
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500">Belum ada data pesanan.</td>
                </tr>
            <?php else: ?>
                <?php foreach($history as $h): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 text-sm text-gray-600">
                        <?= date('d M Y', strtotime($h['tanggal'])) ?><br>
                        <span class="text-xs text-gray-400"><?= date('H:i', strtotime($h['tanggal'])) ?></span>
                    </td>
                    <td class="p-4">
                        <div class="font-bold text-gray-800"><?= $h['nama_pembeli'] ?></div>
                        <div class="text-xs text-gray-500"><?= $h['alamat'] ?? '-' ?></div>
                    </td>
                    <td class="p-4">
                        <div class="font-semibold text-indigo-600"><?= $h['nama_barang'] ?></div>
                        <div class="text-sm text-gray-600">
                            Bahan: <?= $h['nama_bahan'] ?><br>
                            Qty: <?= $h['jumlah_lusin'] ?> Lusin (<?= $h['total_berat'] ?> Kg)
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="font-semibold text-indigo-600"><?= $h['nama_barang'] ?></div>
                        <div class="text-sm text-gray-600">
                            Bahan: <?= $h['nama_bahan'] ?><br>
                            Qty: <?= $h['jumlah_lusin'] ?> Lusin (<?= $h['total_berat'] ?> Kg)
                        </div>
                        <div class="mt-1 font-bold text-gray-800 bg-gray-100 inline-block px-2 rounded text-xs">
                            Total: Rp <?= number_format($h['total_harga'], 0, ',', '.') ?>
                        </div>
                    </td>
                    <td class="p-4">
                        <?php if($h['status'] == 'pending'): ?>
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-bold">
                                <i class="fa-solid fa-clock mr-1"></i> Pending
                            </span>
                        <?php else: ?>
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-bold">
                                <i class="fa-solid fa-check mr-1"></i> Selesai
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>