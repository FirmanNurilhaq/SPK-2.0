<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded shadow">
        <h3 class="font-bold border-b pb-2 mb-4">Data Pesanan</h3>
        <p><strong>Pembeli:</strong> <?= $pesanan['nama_pembeli'] ?></p>
        <p><strong>Barang:</strong> <?= $pesanan['nama_barang'] ?></p>
        <p><strong>Bahan:</strong> <?= $pesanan['nama_bahan'] ?></p>
        <p><strong>Total Kebutuhan:</strong> <?= $pesanan['total_berat'] ?> Kg</p>
        <p class="mt-4 italic text-gray-500">"<?= $pesanan['catatan'] ?>"</p>
    </div>

    <div class="bg-gray-800 text-white p-6 rounded shadow">
        <h3 class="font-bold text-yellow-400 mb-2">Rekomendasi Supplier (AHP)</h3>
        <div id="leaderboardContent">Loading...</div>
        
        <form action="<?= base_url('pengadaan/selesai') ?>" method="post" class="mt-6 border-t border-gray-600 pt-4">
            <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
            <input type="hidden" name="id_jenis_bahan" value="<?= $pesanan['id_jenis_bahan'] ?>">
            
            <label class="block mb-2 text-sm">Pilih Supplier Final:</label>
            <select name="id_supplier" id="supplierSelect" class="w-full text-black p-2 rounded mb-4" required></select>
            
            <button class="w-full bg-yellow-500 text-black font-bold py-2 rounded hover:bg-yellow-400">
                Simpan & Selesaikan
            </button>
        </form>
    </div>
</div>
<script>
    fetch('<?= base_url('pengadaan/get-leaderboard/'.$pesanan['id_jenis_bahan']) ?>')
        .then(response => response.json())
        .then(data => {
            let html = '<table class="w-full text-sm">';
            let select = document.getElementById('supplierSelect');
            select.innerHTML = ''; 
            data.forEach((item, index) => {
                html += `<tr><td class="py-1 text-gray-400">#${index+1}</td><td class="py-1 font-bold">${item.nama}</td><td class="py-1 text-right text-yellow-200">${parseFloat(item.skor_akhir).toFixed(4)}</td></tr>`;
                let option = document.createElement('option');
                option.value = item.id_supplier;
                option.text = `Rank #${index+1} - ${item.nama}`;
                if(index === 0) option.selected = true;
                select.appendChild(option);
            });
            html += '</table>';
            document.getElementById('leaderboardContent').innerHTML = html;
        });
</script>
<?= $this->endSection() ?>