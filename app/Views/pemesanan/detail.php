<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="<?= base_url('pemesanan') ?>" class="text-gray-500 hover:text-indigo-600"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <div class="bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Detail Pesanan #<?= $pesanan['id_pesanan'] ?></h1>
                <p class="text-sm text-gray-600"><?= date('d F Y, H:i', strtotime($pesanan['tanggal'])) ?></p>
            </div>
            <div class="text-right">
                <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-bold">
                    Selesai
                </span>
            </div>
        </div>

        <div class="p-6 grid grid-cols-2 gap-4 border-b">
            <div>
                <p class="text-gray-500 text-sm">Bahan Baku</p>
                <p class="font-bold text-lg"><?= $pesanan['bahan_baku'] ?></p>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Jumlah</p>
                <p class="font-bold text-lg"><?= $pesanan['jumlah_lusin'] ?> Lusin</p>
            </div>
            <div class="col-span-2 mt-2">
                <p class="text-gray-500 text-sm">Supplier Terpilih</p>
                <p class="font-bold text-indigo-700 text-xl"><?= $pesanan['nama_supplier'] ?></p>
                <p class="text-gray-500 text-sm italic">"<?= $pesanan['catatan'] ?: '-' ?>"</p>
            </div>
        </div>

        <div class="p-6 bg-yellow-50">
            <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                <i class="fa-solid fa-history mr-2"></i> Hasil Perhitungan AHP Saat Pemesanan Ini
            </h3>
            <p class="text-sm text-gray-600 mb-4">Data ini adalah "Snapshot" (rekaman tetap) kondisi ranking saat transaksi dilakukan, meskipun bobot master berubah dikemudian hari.</p>

            <table class="min-w-full bg-white border rounded">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 text-xs uppercase">
                        <th class="py-2 px-4 text-left">Ranking</th>
                        <th class="py-2 px-4 text-left">Supplier</th>
                        <th class="py-2 px-4 text-right">Skor Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                    <tr class="<?= ($h['id_supplier'] == $pesanan['id_supplier_terpilih']) ? 'bg-indigo-50 border-l-4 border-indigo-500' : '' ?>">
                        <td class="py-2 px-4 font-bold">#<?= $h['ranking'] ?></td>
                        <td class="py-2 px-4">
                            <?= $h['nama_supplier'] ?>
                            <?php if($h['id_supplier'] == $pesanan['id_supplier_terpilih']): ?>
                                <span class="ml-2 text-xs bg-indigo-600 text-white px-2 py-0.5 rounded">Dipilih</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-2 px-4 text-right font-mono"><?= number_format($h['skor_ahp'], 4) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>