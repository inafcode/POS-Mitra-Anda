<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductsImport implements ToCollection
{
    // Tambahkan ": void" di akhir deklarasi fungsi ini
    public function collection(Collection $rows): void
    {
        $startImport = false;
        
        foreach ($rows as $row) {
            // Lewati baris judul atas sampai menemukan header "Nama Barang" di kolom indeks 1 (Kolom B)
            if (isset($row[1]) && $row[1] === 'Nama Barang') {
                $startImport = true;
                continue;
            }

            // Mulai simpan data setelah baris header
            if ($startImport && !empty($row[1])) {
                
                // Ubah teks "Tanpa Stok" dari Excel menjadi angka 0
                $stokRaw = $row[3];
                $stokAngka = (strtolower(trim($stokRaw)) === 'tanpa stok' || empty($stokRaw)) ? 0 : (int) $stokRaw;

                Product::create([
                    'nama_item'       => $row[1],
                    'harga_jual_ecer' => $row[2] ?: 0,
                    'stok'            => $stokAngka,
                    'satuan'          => $row[4] ?? 'pcs',
                    'pemilik'         => $row[5] ?? 'Mitra Anda',
                    
                    // Beri nilai default teks (string) agar tidak error 'cannot be null'
                    'tipe'            => 'Barang',
                    'harga_modal'     => 0,
                    'kategori'        => '-',
                ]);
            }
        }
    }
}