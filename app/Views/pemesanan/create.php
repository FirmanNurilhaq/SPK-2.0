<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Buat Pesanan Baru</h2>
        
        <form action="<?= base_url('pemesanan/store') ?>" method="post">
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Pilih Jenis Bahan Baku</label>
                <select id="bahanSelect" name="id_jenis_bahan" class="w-full border rounded px-3 py-3 bg-indigo-50 font-bold text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    <option value="">-- Pilih Bahan --</option>
                    <?php foreach($bahan_list as $b): ?>
                        <option value="<?= $b['id_jenis_bahan'] ?>"><?= $b['nama_bahan'] ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Leaderboard di sebelah kanan akan berubah sesuai bahan yang dipilih.</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Jumlah (Lusin)</label>
                <input type="number" name="jumlah_lusin" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Pilih Supplier</label>
                <select id="supplierSelect" name="id_supplier" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Tunggu Leaderboard --</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Catatan</label>
                <textarea name="catatan" class="w-full border rounded px-3 py-2"></textarea>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded hover:bg-indigo-700">
                Simpan Pesanan
            </button>
        </form>
    </div>

    <div class="bg-gray-800 rounded-lg shadow-xl p-6 text-white">
        <h2 class="text-2xl font-bold mb-2 text-yellow-400"><i class="fa-solid fa-crown mr-2"></i> Rekomendasi AHP</h2>
        <p class="text-gray-400 text-sm mb-4">Hasil perhitungan real-time berdasarkan bobot bahan yang dipilih.</p>
        
        <div id="loadingIndicator" class="hidden text-center py-10">
            <i class="fa-solid fa-circle-notch fa-spin text-4xl"></i>
            <p class="mt-2">Menghitung...</p>
        </div>

        <div id="leaderboardContent">
            <p class="text-center py-10 text-gray-500 border border-dashed border-gray-600 rounded">
                Silakan pilih Jenis Bahan terlebih dahulu.
            </p>
        </div>
    </div>
</div>

<script>
    const bahanSelect = document.getElementById('bahanSelect');
    const leaderboardContent = document.getElementById('leaderboardContent');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const supplierSelect = document.getElementById('supplierSelect');

    bahanSelect.addEventListener('change', function() {
        const idBahan = this.value;
        if(!idBahan) return;

        // UI Loading
        leaderboardContent.classList.add('hidden');
        loadingIndicator.classList.remove('hidden');

        // Fetch Data
        fetch('<?= base_url('pemesanan/get-leaderboard/') ?>' + idBahan)
            .then(response => response.json())
            .then(data => {
                loadingIndicator.classList.add('hidden');
                leaderboardContent.classList.remove('hidden');
                renderLeaderboard(data);
                populateSupplierDropdown(data);
            })
            .catch(error => {
                console.error('Error:', error);
                loadingIndicator.classList.add('hidden');
                leaderboardContent.innerHTML = '<p class="text-red-400 text-center">Gagal memuat data.</p>';
                leaderboardContent.classList.remove('hidden');
            });
    });

    function renderLeaderboard(data) {
        if(data.length === 0) {
            leaderboardContent.innerHTML = '<p class="text-center text-gray-400">Belum ada data bobot untuk bahan ini.</p>';
            return;
        }

        let html = '<table class="w-full text-left"><thead><tr class="text-gray-400 border-b border-gray-700"><th class="py-2">Rank</th><th>Supplier</th><th class="text-right">Skor</th></tr></thead><tbody>';
        
        data.forEach((item, index) => {
            let rowClass = index === 0 ? 'bg-green-900 bg-opacity-30 text-green-300 font-bold' : '';
            let icon = index === 0 ? '👑' : `#${index+1}`;
            
            html += `<tr class="border-b border-gray-700 ${rowClass}">
                <td class="py-3 px-2">${icon}</td>
                <td class="py-3">${item.nama}</td>
                <td class="py-3 text-right font-mono">${parseFloat(item.skor_akhir).toFixed(4)}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        leaderboardContent.innerHTML = html;
    }

    function populateSupplierDropdown(data) {
        supplierSelect.innerHTML = '<option value="">-- Pilih Supplier --</option>';
        data.forEach((item, index) => {
            let label = `Rank #${index+1} - ${item.nama} (Skor: ${parseFloat(item.skor_akhir).toFixed(4)})`;
            let option = document.createElement('option');
            option.value = item.id_supplier;
            option.text = label;
            if(index === 0) option.selected = true; // Auto select juara 1
            supplierSelect.appendChild(option);
        });
    }
</script>

<?= $this->endSection() ?>