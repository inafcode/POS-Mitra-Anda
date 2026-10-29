<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Mitra - Mitra Anda Seponti</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Memuat Font Inter dan Fraunces -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Konfigurasi Tema Tailwind (Sesuai Design Token Global) -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        latar: '#F5EFE6',
                        teks: '#292929',
                        redup: '#6B6257',
                        garis: '#DCD2C2',
                        permukaan: '#EDE5D8',
                        aksen: '#B8532F',
                        'teks-tombol': '#FBF7F0'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Fraunces', 'serif'],
                    }
                }
            }
        }
    </script>

    <!-- CSS Tambahan Khusus untuk Elemen Spesifik -->
    <style>
        body {
            background-color: theme('colors.latar');
            color: theme('colors.teks');
            font-feature-settings: "tnum" 1; /* Tabular numbers */
        }
        
        .input-standar {
            background-color: transparent;
            border: 1px solid theme('colors.garis');
            border-radius: 4px;
            padding: 8px 12px;
            font-family: inherit;
            font-size: 15px;
            color: theme('colors.teks');
            transition: border-color 150ms ease-out;
        }
        
        .input-standar:focus-visible {
            outline: 2px solid theme('colors.teks');
            outline-offset: 2px;
            border-color: transparent;
        }

        .btn-sekunder {
            background: transparent;
            border: 1px solid theme('colors.garis');
            border-radius: 4px;
            color: theme('colors.teks');
            font-size: 13px;
            font-weight: 500;
            padding: 8px 16px;
            cursor: pointer;
            transition: all 120ms ease-out;
        }

        .btn-sekunder:hover {
            background-color: theme('colors.permukaan');
        }

        .btn-aksi-teks {
            background: none;
            border: none;
            color: theme('colors.redup');
            font-size: 13px;
            text-decoration: underline;
            cursor: pointer;
            padding: 0;
        }

        .btn-aksi-teks:hover {
            color: theme('colors.teks');
        }

        /* Hilangkan panah spinner pada input number */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Hilangkan panah spinner pada input number */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* --- TAMBAHKAN KODE INI --- */
        /* Scrollbar khusus untuk area keranjang */
        .wadah-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .wadah-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .wadah-scroll::-webkit-scrollbar-thumb {
            background-color: theme('colors.garis');
            border-radius: 4px;
        }
    </style>
</head>
<body class="min-h-screen antialiased flex flex-col">

    <!-- Header Navbar (Buku Kas Style) -->
    <header class="border-b border-garis sticky top-0 z-50 bg-latar">
        <div class="max-w-[1200px] mx-auto px-6 py-5 flex justify-between items-end">
            <div>
                <h1 class="font-serif text-2xl tracking-tight text-teks leading-none mb-1">Mitra Anda Seponti</h1>
                <p class="text-[13px] text-redup leading-none">Portal Pemesanan Instansi B2B</p>
            </div>
            <div class="flex items-end gap-6 text-[13px]">
                <div class="text-right hidden md:block">
                    <p class="font-medium text-teks mb-1">{{ $user->name }}</p>
                    <p class="text-redup">Tagihan Berjalan: <span class="text-teks font-medium">Rp {{ number_format($totalHutangAktif, 0, ',', '.') }}</span></p>
                </div>
                <form action="/logout" method="POST" class="ml-2 pl-6 border-l border-garis">
                    @csrf
                    <button type="submit" class="btn-aksi-teks">Keluar Sistem</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-[1200px] mx-auto w-full px-6 py-12 flex-1">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
            
            <!-- Bagian Kiri: Katalog Produk (Rasio Lebar) -->
            <div class="lg:col-span-2 flex flex-col gap-8">
                
                <!-- Pesan Flash -->
                @if(session('success'))
                    <div class="border-l-2 border-teks bg-permukaan px-5 py-4 text-[15px]">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="border-l-2 border-aksen bg-permukaan px-5 py-4 text-[15px] text-aksen">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Pencarian -->
                <form action="/portal-instansi" method="GET" class="flex items-end gap-3 pb-6 border-b border-garis">
                    <div class="flex-1">
                        <label class="block text-[13px] text-redup mb-2">Cari di Katalog</label>
                        <input type="text" name="q" value="{{ $searchProduk }}" placeholder="Nama barang atau ATK..." class="input-standar w-full">
                    </div>
                    <button type="submit" class="btn-sekunder h-[39px]">Cari Barang</button>
                    @if($searchProduk)
                        <a href="/portal-instansi" class="btn-aksi-teks ml-2 pb-2">Atur Ulang</a>
                    @endif
                </form>

                <!-- Grid Produk -->
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                    @forelse($products as $prod)
                    <div class="group flex flex-col justify-between pt-2 pb-4 border-b border-transparent hover:border-garis transition-colors">
                        <div>
                            <!-- Foto Produk -->
                            <div class="aspect-square bg-permukaan/50 border border-garis rounded mb-4 overflow-hidden flex items-center justify-center">
                                @if($prod->foto_produk)
                                    <img src="{{ asset('storage/' . $prod->foto_produk) }}" class="w-full h-full object-cover mix-blend-multiply">
                                @else
                                    <span class="text-[13px] text-redup">Tanpa foto</span>
                                @endif
                            </div>
                            
                            <!-- Info Produk -->
                            <h3 class="font-medium text-[15px] mb-1 leading-snug">{{ $prod->nama_item }}</h3>
                            <p class="text-[15px] mb-3">Rp {{ number_format($prod->harga_jual_ecer, 0, ',', '.') }}</p>
                        </div>
                        
                        <form action="/portal-instansi/cart" method="POST" class="flex flex-col gap-2 mt-auto pt-2">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $prod->id }}">
                            
                            <div class="flex justify-between items-center text-[13px] text-redup mb-1">
                                <span>Sisa: {{ $prod->stok }} {{ $prod->satuan }}</span>
                            </div>
                            
                            <div class="flex gap-2">
                                <input type="number" name="qty" value="1" min="1" max="{{ $prod->stok }}" class="input-standar w-16 text-center !p-1 text-[15px]">
                                <button type="submit" class="btn-sekunder flex-1 !px-2">Tambah</button>
                            </div>
                        </form>
                    </div>
                    @empty
                    <div class="col-span-full py-12 text-center text-[15px] text-redup border border-dashed border-garis rounded">
                        Katalog barang belum tersedia atau tidak ditemukan.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Bagian Kanan: Keranjang & Tagihan (Kolom Sempit) -->
            <div class="flex flex-col gap-12">
                
                <!-- Section: Keranjang Belanja -->
                <div>
                    <h2 class="font-serif text-xl mb-6">Keranjang Belanja</h2>
                    
                    <!-- UBAH BARIS INI: Tambahkan max-h, overflow-y-auto, dan wadah-scroll -->
                    <div class="border-t border-b border-garis flex flex-col max-h-[360px] overflow-y-auto wadah-scroll pr-2 -mr-2">
                        
                        @php $totalCart = 0; @endphp
                        @forelse($cartItems as $item)
                            @php 
                                $sub = $item->product->harga_jual_ecer * $item->qty; 
                                $totalCart += $sub; 
                            @endphp
                            <!-- Hapus -mx-2 px-2 lama, ganti padding agar tidak tertabrak scrollbar -->
                            <div class="py-4 border-b border-garis last:border-b-0 flex justify-between items-start group hover:bg-permukaan/30 transition-colors pr-2">
                                <div>
                                    <p class="text-[15px] font-medium leading-tight mb-1">{{ $item->product->nama_item }}</p>
                                    <p class="text-[13px] text-redup mb-2">{{ $item->qty }} {{ $item->product->satuan }} × Rp {{ number_format($item->product->harga_jual_ecer, 0, ',', '.') }}</p>
                                    <p class="text-[15px]">Rp {{ number_format($sub, 0, ',', '.') }}</p>
                                </div>
                                <form action="/portal-instansi/cart/{{ $item->id }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-aksi-teks opacity-0 group-hover:opacity-100 transition-opacity" title="Hapus Barang">Hapus</button>
                                </form>
                            </div>
                        @empty
                            <div class="py-8 text-[15px] text-redup">
                                Keranjang saat ini kosong.
                            </div>
                        @endforelse
                    </div>

                    <!-- Area Checkout -->
                    @if($cartItems->isNotEmpty())
                    <div class="pt-6">
                        <div class="flex justify-between items-end mb-6">
                            <span class="text-[15px] text-redup">Total Pesanan</span>
                            <span class="font-serif text-2xl">Rp {{ number_format($totalCart, 0, ',', '.') }}</span>
                        </div>

                        <form action="/portal-instansi/checkout" method="POST" id="formCheckout" onsubmit="siapkanTab()">
                            @csrf
                            <div class="mb-6">
                                <label class="block text-[13px] text-redup mb-2">Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="pilihanMetode" class="input-standar w-full">
                                    <option value="Bon / Tempo">Bon / Hutang Tempo</option>
                                    <option value="Transfer">Transfer Bank</option>
                                    <option value="Tunai">Tunai / Cash (Bayar Langsung)</option>
                                </select>
                            </div>
                            
                            <!-- SATU-SATUNYA TOMBOL AKSEN TERAKOTA DI HALAMAN INI -->
                            <button type="submit" class="w-full bg-aksen text-teks-tombol font-medium py-3 rounded hover:opacity-90 transition-opacity focus-visible:outline-2 focus-visible:outline-teks focus-visible:outline-offset-2" onclick="return confirm('Kirim pesanan ini ke toko?')">
                                Buat Pesanan
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <!-- Section: Riwayat Tagihan Aktif -->
                <div>
                    <h2 class="font-serif text-xl mb-6">Bon Belum Lunas</h2>
                    
                    <div class="border-t border-garis flex flex-col">
                        @forelse($tagihanAktif as $tagihan)
                            <div class="py-4 border-b border-garis flex justify-between items-start">
                                <div>
                                    <a href="#" class="text-[15px] font-medium underline decoration-garis underline-offset-4 mb-1 inline-block">{{ $tagihan->kode_nota }}</a>
                                    <p class="text-[13px] text-redup mb-2">{{ \Carbon\Carbon::parse($tagihan->created_at)->translatedFormat('l, d F Y') ?? date('d M Y', strtotime($tagihan->created_at)) }}</p>
                                    
                                    <!-- Status Menggunakan Titik 6px (Sesuai Aturan) -->
                                    <div class="flex items-center gap-2 text-[13px]">
                                        @if($tagihan->status_pembayaran === 'Menunggu Pembayaran')
                                            <span class="block w-1.5 h-1.5 rounded-full" style="background-color: #A87B2A;"></span>
                                            <span style="color: #A87B2A;">Sedang dicek admin</span>
                                        @else
                                            <span class="block w-1.5 h-1.5 rounded-full" style="background-color: #7A6F64;"></span>
                                            <span style="color: #7A6F64;">Belum Lunas (Bon)</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[15px]">Rp {{ number_format($tagihan->total_bayar, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-[15px] text-redup border-b border-garis">
                                Tidak ada catatan bon aktif.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        function siapkanTab() {
            let metode = document.getElementById('pilihanMetode').value;
            let form = document.getElementById('formCheckout');
            
            if (metode === 'Transfer') {
                form.target = "_blank";
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                form.target = "_self";
            }
        }
    </script>
</body>
</html>