<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<h2 class="text-xl font-bold mb-4">Daftar Pesanan Masuk (Pending)</h2>
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="p-3">Tanggal</th>
                <th class="p-3">Pembeli & Barang</th>
                <th class="p-3">Bahan & Berat</th>
                <th class="p-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($pending as $p): ?>
            <tr class="border-b">
                <td class="p-3 text-sm"><?= $p['tanggal'] ?></td>
                <td class="p-3">
                    <div class="font-bold"><?= $p['nama_pembeli'] ?></div>
                    <div class="text-sm"><?= $p['nama_barang'] ?></div>
                </td>
                <td class="p-3">
                    <div class="font-bold text-indigo-600"><?= $p['nama_bahan'] ?></div>
                    <div class="text-sm"><?= $p['total_berat'] ?> Kg (<?= $p['jumlah_lusin'] ?> Lusin)</div>
                </td>
                <td class="p-3 text-center">
                    <a href="<?= base_url('pengadaan/proses/'.$p['id_pesanan']) ?>" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                        Proses AHP
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>