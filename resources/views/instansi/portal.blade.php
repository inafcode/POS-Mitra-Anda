<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Mitra - Mitra Anda Seponti</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800">

    <!-- Header Navbar -->
    <div class="bg-slate-900 text-white p-4 shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-xl font-black text-amber-400">Portal Instansi & B2B</h1>
                <p class="text-xs text-slate-400">Toko Mitra Anda Seponti</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="font-bold text-sm">{{ $user->name }}</p>
                    <p class="text-xs text-amber-400">Total Tagihan: Rp{{ number_format($totalHutangAktif, 0, ',', '.') }}</p>
                </div>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg text-sm transition">Keluar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto p-4 lg:p-6 grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
        
        <!-- Bagian Kiri: Katalog Produk -->
        <div class="lg:col-span-2 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded shadow-sm font-bold">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-800 p-4 rounded shadow-sm font-bold">{{ session('error') }}</div>
            @endif

            <!-- Pencarian -->
            <form action="/portal-instansi" method="GET" class="flex gap-2">
                <input type="text" name="q" value="{{ $searchProduk }}" placeholder="Cari ATK, Barang Cetakan..." class="flex-1 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl shadow-sm transition">Cari</button>
            </form>

            <!-- Grid Produk -->
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($products as $prod)
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="h-32 bg-slate-100 rounded-lg mb-3 flex items-center justify-center overflow-hidden">
                            @if($prod->foto_produk)
                                <img src="{{ asset('storage/' . $prod->foto_produk) }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-4xl">📦</span>
                            @endif
                        </div>
                        <h3 class="font-bold text-sm mb-1 leading-tight">{{ $prod->nama_item }}</h3>
                        <p class="text-blue-600 font-black mb-1">Rp{{ number_format($prod->harga_jual_ecer, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mb-3">Sisa Stok: <span class="font-bold">{{ $prod->stok }} {{ $prod->satuan }}</span></p>
                    </div>
                    
                    <form action="/portal-instansi/cart" method="POST" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $prod->id }}">
                        <input type="number" name="qty" value="1" min="1" max="{{ $prod->stok }}" class="w-16 border border-gray-300 rounded-lg text-center font-bold outline-none focus:border-blue-500">
                        <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs py-2 rounded-lg transition">+ Keranjang</button>
                    </form>
                </div>
                @empty
                <div class="col-span-full bg-white p-8 rounded-2xl text-center text-gray-500 shadow-sm border border-gray-100">Barang tidak ditemukan.</div>
                @endforelse
            </div>
        </div>

        <!-- Bagian Kanan: Keranjang & Tagihan -->
        <div class="space-y-6">
            
            <!-- Keranjang Belanja -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-blue-600 p-4">
                    <h2 class="font-black text-white flex items-center gap-2">🛒 Keranjang Belanja</h2>
                </div>
                <div class="p-4 space-y-4">
                    @php $totalCart = 0; @endphp
                    @forelse($cartItems as $item)
                        @php $sub = $item->product->harga_jual_ecer * $item->qty; $totalCart += $sub; @endphp
                        <div class="flex justify-between items-start border-b border-gray-100 pb-3">
                            <div class="flex-1">
                                <p class="font-bold text-sm">{{ $item->product->nama_item }}</p>
                                <p class="text-xs text-gray-500">{{ $item->qty }} {{ $item->product->satuan }} x Rp{{ number_format($item->product->harga_jual_ecer, 0, ',', '.') }}</p>
                                <p class="font-black text-sm text-slate-800 mt-1">Rp{{ number_format($sub, 0, ',', '.') }}</p>
                            </div>
                            <form action="/portal-instansi/cart/{{ $item->id }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-1 font-black text-lg" title="Hapus">✕</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-400 py-4">Keranjang masih kosong.</p>
                    @endforelse
                </div>
                
                @if($cartItems->isNotEmpty())
                <div class="p-4 bg-slate-50 border-t border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <span class="font-bold text-slate-600">Total Pesanan:</span>
                        <span class="font-black text-lg text-blue-600">Rp{{ number_format($totalCart, 0, ',', '.') }}</span>
                    </div>
                    <form action="/portal-instansi/checkout" method="POST" id="formCheckout" onsubmit="siapkanTab()" class="space-y-3">
                        @csrf
                        
                        <!-- Pilihan Metode Pembayaran -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Metode Pembayaran</label>
                            <select name="metode_pembayaran" id="pilihanMetode" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 outline-none focus:border-blue-500">
                                <option value="Bon / Tempo">📝 Bon / Hutang Tempo</option>
                                <option value="Transfer">💳 Transfer Bank</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-900 font-black py-3 rounded-xl shadow-md transition" onclick="return confirm('Proses pesanan ini sekarang?')">
                            ✅ PROSES CHECKOUT SEKARANG
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <!-- Info Riwayat Tagihan -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                <h3 class="font-black text-slate-800 mb-2 border-b pb-2">📋 Tagihan Aktif Belum Lunas</h3>
                @forelse($tagihanAktif as $tagihan)
                    <div class="py-2 border-b border-gray-50 last:border-0 flex justify-between items-center">
                        <div>
                            <p class="font-bold text-xs">{{ $tagihan->kode_nota }}</p>
                            <p class="text-[10px] text-gray-500">{{ date('d M Y', strtotime($tagihan->created_at)) }}</p>
                            <!-- Badge Status -->
                            @if($tagihan->status_pembayaran === 'Menunggu Pembayaran')
                                <span class="inline-block mt-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-[9px] font-black rounded uppercase">Sedang Dicek Admin</span>
                            @else
                                <span class="inline-block mt-1 px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-black rounded uppercase">Belum Lunas (Bon)</span>
                            @endif
                        </div>
                        <p class="font-black text-sm text-red-600">Rp{{ number_format($tagihan->total_bayar, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <p class="text-center text-sm text-gray-400 py-4">Tidak ada tagihan bon aktif.</p>
                @endforelse
            </div>

        </div>
    </div>

    <script>
        function siapkanTab() {
            let metode = document.getElementById('pilihanMetode').value;
            let form = document.getElementById('formCheckout');
            
            if (metode === 'Transfer') {
                // Jika transfer, paksa form mengirim data dan memproses link WA di Tab Baru
                form.target = "_blank";
                
                // Segarkan/refresh halaman asli (keranjang) setelah 2 detik agar keranjang kosong dan riwayat tagihan terupdate
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                // Jika Bon, tetap proses di tab yang sama seperti biasa
                form.target = "_self";
            }
        }
    </script>
</body>
</html>
</body>
</html>