<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <div class="bg-gradient-to-br from-indigo-600 to-blue-500 rounded-lg shadow-xl p-6 text-white h-fit">
        <h2 class="text-2xl font-bold mb-2"><i class="fa-solid fa-trophy text-yellow-300 mr-2"></i> Rekomendasi Supplier</h2>
        <p class="mb-4 text-blue-100 opacity-90">Berdasarkan bobot kriteria dan penilaian supplier saat ini.</p>

        <div class="overflow-hidden rounded-lg border border-indigo-400">
            <table class="min-w-full bg-indigo-700 bg-opacity-25 text-white">
                <thead>
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-semibold uppercase tracking-wider">Rank</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold uppercase tracking-wider">Supplier</th>
                        <th class="py-3 px-4 text-right text-sm font-semibold uppercase tracking-wider">Skor AHP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-400">
                    <?php if(empty($leaderboard)): ?>
                        <tr><td colspan="3" class="p-4 text-center">Belum ada data supplier / bobot belum dihitung lengkap.</td></tr>
                    <?php else: ?>
                        <?php foreach($leaderboard as $index => $row): ?>
                        <tr class="<?= $index == 0 ? 'bg-green-500 bg-opacity-20 font-bold' : '' ?>">
                            <td class="py-3 px-4">
                                <?php if($index == 0): ?>
                                    <i class="fa-solid fa-crown text-yellow-400"></i> #1
                                <?php else: ?>
                                    #<?= $index + 1 ?>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4"><?= $row['nama'] ?></td>
                            <td class="py-3 px-4 text-right"><?= number_format($row['skor_akhir'], 4) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-xs text-blue-200 italic">
            *Skor tertinggi adalah rekomendasi terbaik sesuai preferensi perusahaan.
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 h-fit">
        <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Form Buat Pesanan</h2>
        
        <form action="<?= base_url('pemesanan/store') ?>" method="post">
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Jumlah Pesanan (Lusin)</label>
                <input type="number" name="jumlah_lusin" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-indigo-500" placeholder="Contoh: 10" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Jenis Bahan Baku</label>
                <input type="text" name="bahan_baku" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-indigo-500" placeholder="Contoh: Cotton Combed 30s" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Pilih Supplier Final</label>
                <p class="text-xs text-gray-500 mb-2">Pilih supplier pemenang atau sesuai keputusan Anda.</p>
                <select name="id_supplier_terpilih" class="w-full border rounded px-3 py-2 bg-gray-50 font-semibold" required>
                    <option value="">-- Pilih Supplier --</option>
                    <?php foreach($leaderboard as $index => $row): ?>
                        <option value="<?= $row['id_supplier'] ?>">
                            Rank #<?= $index+1 ?> - <?= $row['nama'] ?> (Skor: <?= number_format($row['skor_akhir'], 4) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Catatan Tambahan</label>
                <textarea name="catatan" class="w-full border rounded px-3 py-2" rows="2"></textarea>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg shadow hover:bg-indigo-700 transition">
                <i class="fa-solid fa-save mr-2"></i> Simpan Pesanan
            </button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>