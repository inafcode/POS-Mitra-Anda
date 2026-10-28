<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Gudang - Mitra Anda Seponti</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Header Admin -->
    <nav class="bg-slate-900 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <span class="text-white text-xl font-bold">Ruang Admin / Gudang</span>
                <div class="flex gap-4">
                    <a href="/kasir" class="text-slate-300 hover:text-white text-sm">Ke Kasir</a>
                    <a href="/" class="text-slate-300 hover:text-white text-sm">Ke Toko Online</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Alert Jika Berhasil Tambah/Edit Data -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Alarm Peringatan Stok Menipis (Kurang dari 10) -->
        @php
            $stokTipis = $products->filter(function($item) {
                if ($item->tipe !== 'Barang') return false;
                
                // Jika milik Kado Koeni, beri peringatan HANYA saat stok habis (0)
                if ($item->pemilik === 'Kado Koeni') {
                    return $item->stok <= 0;
                }
                
                // Jika milik Mitra Anda (ATK), beri peringatan saat stok di bawah 5
                return $item->stok < 5;
            });
        @endphp

        @if($stokTipis->count() > 0)
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded shadow-sm mb-6">
                <div class="flex items-center mb-2">
                    <span class="text-amber-800 font-bold text-sm uppercase tracking-wider">
                        ⚠️ Peringatan: Ada {{ $stokTipis->count() }} Barang yang Stoknya Menipis / Habis!
                    </span>
                </div>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($stokTipis as $tipis)
                        <a href="/admin/{{ $tipis->id }}/edit" class="text-xs px-3 py-1 rounded-full font-bold border transition {{ $tipis->stok <= 0 ? 'bg-red-600 text-white border-red-700' : 'bg-white text-amber-900 border-amber-300 hover:bg-amber-100' }}">
                            {{ $tipis->nama_item }} (Sisa: {{ $tipis->stok }} {{ $tipis->satuan }})
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Master Barang</h2>
            <!-- Tombol Tambah Barang -->
            <a href="/admin/create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium shadow-sm transition">
                + Tambah Barang Baru
            </a>
        </div>

        <!-- Tabel Gudang -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="p-4 border-b">Barcode</th>
                        <th class="p-4 border-b text-center">Foto</th>
                        <th class="p-4 border-b">Nama Item</th>
                        <th class="p-4 border-b">Tipe / Kategori</th>
                        <th class="p-4 border-b text-right">Modal</th>
                        <th class="p-4 border-b text-right">Jual Ecer</th>
                        <th class="p-4 border-b text-center">Stok</th>
                        <th class="p-4 border-b text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-mono text-gray-500">{{ $item->barcode ?? '-' }}</td>
                        <td class="p-4 text-center">
                            @if($item->foto_produk)
                                <img src="{{ asset('storage/' . $item->foto_produk) }}" alt="Foto" class="w-12 h-12 object-cover rounded border border-gray-200 mx-auto shadow-sm">
                            @else
                                <div class="w-12 h-12 bg-gray-100 rounded border border-gray-200 flex items-center justify-center text-[10px] text-gray-400 mx-auto">
                                    Kosong
                                </div>
                            @endif
                        </td>
                        <td class="p-4 font-bold text-gray-800">{{ $item->nama_item }}</td>
                        <td class="p-4 text-gray-600">
                            <span class="bg-gray-200 px-2 py-1 rounded text-xs">{{ $item->tipe }}</span>
                            <span class="text-xs ml-1">{{ $item->kategori }}</span>
                        </td>
                        <td class="p-4 text-right text-red-600 font-medium">Rp{{ number_format($item->harga_modal, 0, ',', '.') }}</td>
                        <td class="p-4 text-right text-blue-600 font-bold">Rp{{ number_format($item->harga_jual_ecer, 0, ',', '.') }}</td>
                        <td class="p-4 text-center font-bold {{ $item->stok < 10 ? 'text-red-500' : 'text-gray-700' }}">
                            {{ $item->stok ?? '-' }} {{ $item->satuan }}
                        </td>
                        <td class="p-4 text-center flex justify-center gap-1">
                            <!-- Tombol Edit -->
                            <a href="/admin/{{ $item->id }}/edit" class="bg-amber-100 text-amber-700 hover:bg-amber-200 px-3 py-1 rounded text-xs font-bold">Edit</a>
                            
                            <!-- Tombol Hapus (Dalam Laravel, hapus data wajib menggunakan Form) -->
                            <form action="/admin/{{ $item->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini secara permanen?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded text-xs font-bold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
    </main>
</body>
</html>