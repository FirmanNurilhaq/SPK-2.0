<?php

namespace App\Libraries;

class AhpCalculator
{
    /**
     * Menghitung Eigen Vector (Bobot Prioritas) dari Matriks Perbandingan
     * @param array $matrixData Array [id_baris][id_kolom] = nilai
     * @param array $items Array daftar ID item (kriteria/supplier)
     * @return array Array [id_item] => bobot (decimal)
     */
    public function hitungBobot($matrixData, $items)
    {
        $ids = array_column($items, 'id'); // Ambil list ID saja
        $jumlah_kolom = [];

        // 1. Hitung Jumlah Per Kolom
        foreach ($ids as $colId) {
            $sum = 0;
            foreach ($ids as $rowId) {
                // Jika tidak ada data di DB, asumsikan nilai 1 (diagonal) atau hitung balik
                $nilai = $this->getNilaiMatrix($matrixData, $rowId, $colId);
                $sum += $nilai;
            }
            $jumlah_kolom[$colId] = $sum;
        }

        // 2. Normalisasi Matriks & Hitung Rata-rata Baris (Bobot)
        $bobot_prioritas = [];
        
        foreach ($ids as $rowId) {
            $total_baris_normalized = 0;
            foreach ($ids as $colId) {
                $nilai_asli = $this->getNilaiMatrix($matrixData, $rowId, $colId);
                // Bagi nilai sel dengan jumlah kolomnya
                $normalized = $nilai_asli / $jumlah_kolom[$colId];
                $total_baris_normalized += $normalized;
            }
            // Rata-rata baris = Bobot
            $bobot_prioritas[$rowId] = $total_baris_normalized / count($ids);
        }

        return $bobot_prioritas;
    }

    /**
     * Helper untuk mengambil nilai matriks aman (menangani bolak-balik A vs B)
     */
    private function getNilaiMatrix($matrixData, $rowId, $colId)
    {
        if ($rowId == $colId) return 1; // Diagonal selalu 1

        // Cek jika ada nilai langsung (Row vs Col)
        if (isset($matrixData[$rowId][$colId])) {
            return $matrixData[$rowId][$colId];
        }

        // Cek kebalikannya (Col vs Row), jika ada, nilainya 1/x
        if (isset($matrixData[$colId][$rowId])) {
            return 1 / $matrixData[$colId][$rowId];
        }

        return 1; // Default aman jika data kosong
    }
}