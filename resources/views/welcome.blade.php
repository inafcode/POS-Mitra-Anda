<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mitra Anda Seponti</title>
    <!-- Memanggil Tailwind CSS untuk desain cepat -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Bagian Atas (Navbar) -->
    <nav class="bg-blue-600 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-white text-xl font-bold">Mitra Anda Seponti</span>
                </div>
                <div>
                    <a href="#" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-md text-sm font-medium transition shadow-sm">
                        Login Instansi
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Bagian Konten Utama -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Katalog Produk & Layanan</h2>

        <!-- Cek apakah database masih kosong -->
        @if($products->isEmpty())
            <div class="text-center py-12 bg-white rounded-lg shadow border border-gray-200">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada barang</h3>
                <p class="mt-1 text-sm text-gray-500">Database gudang Anda saat ini masih kosong.</p>
            </div>
        @else
            <!-- Menampilkan Barang Otomatis dari Database -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                
                <!-- Perulangan (Looping) untuk setiap barang yang ada di database -->
                @foreach($products as $item)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition relative flex flex-col">
                    
                    <!-- Munculkan Label JASA jika tipe item adalah Jasa -->
                    @if($item->tipe == 'Jasa')
                        <span class="absolute top-2 right-2 bg-purple-100 text-purple-800 text-xs font-bold px-2 py-1 rounded shadow-sm z-10">JASA</span>
                    @endif
                    
                    <!-- Gambar Placeholder Sementara -->
                    <!-- Menampilkan Foto Produk atau Placeholder -->
                 @if($item->foto_produk)
                     <div class="h-40 w-full overflow-hidden bg-white flex items-center justify-center border-b border-gray-100">
                         <img src="{{ asset('storage/' . $item->foto_produk) }}" alt="{{ $item->nama_item }}" class="object-contain h-full w-full p-2">
                     </div>
                 @else
                     <div class="h-40 bg-gray-100 flex items-center justify-center p-2 text-center border-b border-gray-200">
                         <svg class="w-8 h-8 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                     </div>
                 @endif
                    
                    <div class="p-4 flex flex-col flex-grow">
                        <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide">{{ $item->kategori }}</p>
                        <h3 class="text-sm font-bold text-gray-800 mb-2 leading-tight flex-grow">{{ $item->nama_item }}</h3>
                        
                        <div>
                            <p class="text-lg font-extrabold text-blue-600">Rp{{ number_format($item->harga_jual_ecer, 0, ',', '.') }}</p>
                        </div>
                        
                        <div class="mt-3 flex justify-between items-center text-xs pt-3 border-t border-gray-100">
                            @if($item->tipe == 'Barang')
                                <span class="text-gray-600 font-medium">Stok: <span class="text-gray-900">{{ $item->stok }}</span> {{ $item->satuan }}</span>
                            @else
                                <span class="text-gray-600 font-medium">Satuan: {{ $item->satuan }}</span>
                            @endif
                        </div>
                        
                        <button class="mt-4 w-full bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white border border-blue-200 hover:border-blue-600 py-2 rounded text-sm font-bold transition">
                            + Keranjang
                        </button>
                    </div>
                </div>
                @endforeach
                
            </div>
        @endif
    </main>
</body>
</html>