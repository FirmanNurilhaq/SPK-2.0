<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Input Pesanan Pembeli</h2>
    <form action="<?= base_url('pemesanan/store') ?>" method="post">
        
        <div class="mb-4">
            <label class="font-bold">Data Pembeli</label>
            <select name="id_pembeli" class="w-full border p-2 rounded">
                <?php foreach($pembeli as $p): ?>
                    <option value="<?= $p['id_pembeli'] ?>"><?= $p['nama_pembeli'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-4">
            <label class="font-bold">Nama Barang</label>
            <input type="text" name="nama_barang" class="w-full border p-2 rounded" required placeholder="Contoh: Kaos Polos Lengan Panjang">
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="font-bold">Jenis Bahan</label>
                <select name="id_jenis_bahan" id="jenisBahan" class="w-full border p-2 rounded" onchange="hitungEstimasi()">
                    <?php foreach($bahan as $b): ?>
                        <option value="<?= $b['id_jenis_bahan'] ?>" data-harga="<?= $b['harga'] ?>">
                            <?= $b['nama_bahan'] ?> (Rp <?= number_format($b['harga'], 0, ',', '.') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="font-bold">Jumlah (Lusin)</label>
                <input type="number" id="lusin" name="jumlah_lusin" class="w-full border p-2 rounded" required oninput="hitungEstimasi()" placeholder="0">
            </div>
        </div>

        <div class="mb-4 grid grid-cols-2 gap-4">
            <div class="bg-gray-100 p-3 rounded">
                <p class="text-sm text-gray-600">Estimasi Berat:</p>
                <p class="font-bold text-lg text-gray-800"><span id="beratInfo">0</span> Kg</p>
                <p class="text-xs italic text-gray-500">2.7 Kg / Lusin</p>
            </div>
            <div class="bg-indigo-50 p-3 rounded border border-indigo-100">
                <p class="text-sm text-indigo-600">Estimasi Total Harga:</p>
                <p class="font-bold text-xl text-indigo-700">Rp <span id="hargaInfo">0</span></p>
            </div>
        </div>

        <div class="mb-4">
            <label class="font-bold">Catatan</label>
            <textarea name="catatan" class="w-full border p-2 rounded"></textarea>
        </div>

        <button class="bg-indigo-600 text-white px-4 py-2 rounded font-bold w-full hover:bg-indigo-700">Simpan Pesanan</button>
    </form>
</div>

<script>
function hitungEstimasi() {
    let lusin = parseFloat(document.getElementById('lusin').value) || 0;
    
    // Ambil harga dari atribut data-harga di option yang dipilih
    let selectBahan = document.getElementById('jenisBahan');
    let hargaSatuan = parseFloat(selectBahan.options[selectBahan.selectedIndex].getAttribute('data-harga')) || 0;

    // Hitung
    let berat = lusin * 2.7;
    let totalHarga = lusin * hargaSatuan;

    // Tampilkan
    document.getElementById('beratInfo').innerText = berat.toFixed(2);
    document.getElementById('hargaInfo').innerText = new Intl.NumberFormat('id-ID').format(totalHarga);
}

// Jalankan sekali saat load agar angka tidak kosong jika browser auto-fill
window.onload = hitungEstimasi;
</script>
<?= $this->endSection() ?>