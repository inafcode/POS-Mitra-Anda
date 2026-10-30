<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - Mitra Anda Seponti</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --latar: #F5EFE6;
            --teks: #292929;
            --teks-redup: #6B6257;
            --garis: #DCD2C2;
            --permukaan: #EDE5D8;
            --aksen: #B8532F;
        }

        body {
            background-color: var(--latar);
            color: var(--teks);
            font-feature-settings: "tnum" 1;
        }

        a:focus-visible, button:focus-visible, input:focus-visible {
            outline: 2px solid var(--teks);
            outline-offset: 2px;
        }

        .btn-utama {
            background-color: var(--aksen);
            color: #FBF7F0;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 4px;
            transition: opacity 150ms ease-out;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-utama:hover { opacity: 0.9; }

        .btn-sekunder {
            background-color: transparent;
            color: var(--teks);
            border: 1px solid var(--garis);
            font-weight: 500;
            font-size: 13px;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background-color 150ms ease-out;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        .btn-sekunder:hover { background-color: var(--permukaan); }

        .input-cari {
            background: transparent;
            border: none;
            border-bottom: 1px solid var(--garis);
            padding: 8px 0;
            font-size: 15px;
            color: var(--teks);
            width: 100%;
            border-radius: 0;
            transition: border-color 150ms ease;
        }
        .input-cari:focus {
            outline: none;
            border-bottom: 2px solid var(--teks);
        }
        .input-cari::placeholder {
            color: var(--teks-redup);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;  
            overflow: hidden;
        }
        
        .grid-garis > div {
            border-right: 1px solid var(--garis);
            border-bottom: 1px solid var(--garis);
        }
        .grid-garis > div:nth-child(2n) { border-right: none; }
        @media (min-width: 640px) {
            .grid-garis > div:nth-child(2n) { border-right: 1px solid var(--garis); }
            .grid-garis > div:nth-child(3n) { border-right: none; }
        }
        @media (min-width: 768px) {
            .grid-garis > div:nth-child(3n) { border-right: 1px solid var(--garis); }
            .grid-garis > div:nth-child(4n) { border-right: none; }
        }
        @media (min-width: 1024px) {
            .grid-garis > div:nth-child(4n) { border-right: 1px solid var(--garis); }
            .grid-garis > div:nth-child(5n) { border-right: none; }
        }

        /* Navigasi Slider Panah & Dots */
        .slider-arrow {
            opacity: 0;
            visibility: hidden;
            transition: all 200ms ease;
        }
        .slider-container:hover .slider-arrow {
            opacity: 1;
            visibility: visible;
        }
        .dot {
            transition: background-color 200ms ease;
        }
        .dot.active {
            background-color: var(--latar); /* Warna aktif terang */
        }
    </style>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        latar: 'var(--latar)',
                        teks: 'var(--teks)',
                        redup: 'var(--teks-redup)',
                        garis: 'var(--garis)',
                        permukaan: 'var(--permukaan)',
                        aksen: 'var(--aksen)',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Fraunces', 'serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen antialiased flex flex-col">

    <!-- Header / Navbar -->
    <nav class="border-b border-garis bg-latar relative z-50">
        <div class="max-w-[1200px] mx-auto px-6 h-[72px] flex justify-between items-center">
            <div class="flex-shrink-0">
                <span class="font-serif text-2xl text-teks font-medium tracking-tight">Mitra Anda Seponti</span>
            </div>
            
            <div class="flex-grow max-w-xl mx-8 hidden md:block">
                <form action="/" method="GET" class="relative w-full">
                    <input type="text" name="search" placeholder="Cari barang atau layanan..." class="input-cari text-sm" value="{{ request('search') }}">
                </form>
            </div>
            
            <div>
                <!-- LOGIKA SMART BUTTON -->
                @auth
                    <a href="/admin" class="btn-utama shadow-none" style="background-color: var(--teks);">
                        Ke Dashboard &rarr;
                    </a>
                @else
                    <a href="/login" class="btn-utama shadow-none">
                        Login Instansi
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-1 w-full pb-16">
        
        <!-- BANNER SLIDER TOKOPEDIA STYLE -->
        <section class="max-w-[1200px] mx-auto px-6 py-6">
            <!-- Wadah Slider (Border Radius 6px sesuai aturan) -->
            <div class="slider-container relative w-full h-[200px] sm:h-[300px] md:h-[380px] rounded-md overflow-hidden bg-permukaan/50 border border-garis">
                
                <!-- Track Gambar -->
                <div class="flex h-full w-full transition-transform duration-500 ease-out" id="slider-track">
                    
                    <!-- Slide 1: Stempel -->
                    <div class="w-full h-full flex-shrink-0 cursor-pointer relative" onclick="document.querySelector('.input-cari').value='Stempel'; document.querySelector('.input-cari').form.submit();">
                        <img src="{{ asset('img/banner-stempel.jpg') }}" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9IiNFREU1RDgiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9InNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMjAiIGZpbGw9IiM2QjYyNTciIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5CYW5uZXIgU3RlbXBlbDwvdGV4dD48L3N2Zz4='" alt="Promo Stempel" class="w-full h-full object-cover">
                    </div>

                    <!-- Slide 2: Buket -->
                    <div class="w-full h-full flex-shrink-0 cursor-pointer relative" onclick="document.querySelector('.input-cari').value='Buket'; document.querySelector('.input-cari').form.submit();">
                        <img src="{{ asset('img/banner-buket.jpg') }}" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9IiNFREU1RDgiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9InNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMjAiIGZpbGw9IiM2QjYyNTciIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5CYW5uZXIgQnVrZXQ8L3RleHQ+PC9zdmc+'" alt="Promo Buket" class="w-full h-full object-cover">
                    </div>

                    <!-- Slide 3: Website -->
                    <div class="w-full h-full flex-shrink-0 cursor-pointer relative" onclick="document.querySelector('.input-cari').value='Website'; document.querySelector('.input-cari').form.submit();">
                        <img src="{{ asset('img/banner-web.jpg') }}" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9IiNFREU1RDgiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9InNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMjAiIGZpbGw9IiM2QjYyNTciIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5CYW5uZXIgV2Vic2l0ZTwvdGV4dD48L3N2Zz4='" alt="Promo Website" class="w-full h-full object-cover">
                    </div>

                </div>

                <!-- Tombol Navigasi Kiri (Panah) -->
                <button onclick="prevSlide()" class="slider-arrow absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-latar/90 border border-garis rounded-full flex items-center justify-center text-teks hover:bg-latar z-10">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                </button>

                <!-- Tombol Navigasi Kanan (Panah) -->
                <button onclick="nextSlide()" class="slider-arrow absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-latar/90 border border-garis rounded-full flex items-center justify-center text-teks hover:bg-latar z-10">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </button>

                <!-- Titik Navigasi (Dots) di Kiri Bawah -->
                <div class="absolute bottom-4 left-6 flex gap-2 z-10">
                    <button onclick="goToSlide(0)" class="dot active w-2 h-2 rounded-full bg-garis/60 hover:bg-latar/80"></button>
                    <button onclick="goToSlide(1)" class="dot w-2 h-2 rounded-full bg-garis/60 hover:bg-latar/80"></button>
                    <button onclick="goToSlide(2)" class="dot w-2 h-2 rounded-full bg-garis/60 hover:bg-latar/80"></button>
                </div>

            </div>
        </section>

        <!-- AREA GRID PRODUK & KATEGORI -->
        <section class="max-w-[1200px] mx-auto pt-6">
            
            <div class="px-6 mb-6">
                <h2 class="font-serif text-2xl text-teks">Katalog Utama</h2>
                <p class="text-[14px] text-redup">Ketersediaan barang dan layanan toko.</p>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-24 px-6 border-b border-garis">
                    <div class="text-[16px] text-teks font-medium mb-2">Pencarian kosong</div>
                    <p class="text-[15px] text-redup">Tidak ada barang yang ditemukan dalam catatan gudang saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 grid-garis border-l border-t border-garis mx-6">
                    
                    @foreach($products as $item)
                    @php 
                        $isHabis = ($item->tipe == 'Barang' && $item->stok <= 0); 
                    @endphp

                    <div class="flex flex-col p-4 group transition-colors hover:bg-permukaan/30 {{ $isHabis ? 'opacity-60' : '' }}">
                        
                        <div class="aspect-square bg-permukaan/40 border border-garis rounded mb-3 relative flex items-center justify-center overflow-hidden">
                            @if($item->tipe == 'Jasa')
                                <span class="absolute top-2 right-2 text-[10px] uppercase tracking-widest text-teks bg-latar border border-garis px-2 py-0.5 rounded-sm z-10 font-medium">Jasa</span>
                            @endif

                            @if($isHabis)
                                <div class="absolute inset-0 bg-latar/70 z-10 flex items-center justify-center backdrop-blur-[1px]">
                                    <span class="bg-teks text-latar text-[11px] uppercase tracking-widest px-3 py-1 font-medium rounded-sm">Habis</span>
                                </div>
                            @endif
                            
                            @if($item->foto_produk)
                                <img src="{{ asset('storage/' . $item->foto_produk) }}" alt="{{ $item->nama_item }}" class="w-full h-full object-cover mix-blend-multiply p-2 transition-transform duration-500 group-hover:scale-105">
                            @else
                                <span class="text-[12px] text-redup">Tanpa foto</span>
                            @endif
                        </div>
                        
                        <div class="flex flex-col flex-grow">
                            <p class="text-[10px] uppercase tracking-widest text-redup mb-1">{{ $item->kategori }}</p>
                            <h3 class="font-medium text-[14px] text-teks leading-snug mb-2 line-clamp-2 h-[40px]" title="{{ $item->nama_item }}">
                                {{ $item->nama_item }}
                            </h3>
                            <div class="font-serif text-[17px] text-teks mb-3">
                                Rp {{ number_format($item->harga_jual_ecer, 0, ',', '.') }}
                            </div>
                            
                            <div class="mt-auto border-t border-garis/50 pt-3">
                                <div class="flex justify-between items-center text-[12px] text-redup mb-3">
                                    @if($item->tipe == 'Barang')
                                        <span>Stok: <span class="text-teks font-medium">{{ $item->stok }}</span></span>
                                    @else
                                        <span>Satuan: <span class="text-teks">{{ $item->satuan }}</span></span>
                                    @endif
                                    <span>{{ $item->tipe == 'Barang' ? $item->satuan : '' }}</span>
                                </div>
                                
                                <form action="#" method="POST">
                                    <button type="button" class="btn-sekunder {{ $isHabis ? 'cursor-not-allowed opacity-50' : '' }}" {{ $isHabis ? 'disabled' : '' }}>
                                        {{ $isHabis ? 'Stok Kosong' : '+ Keranjang' }}
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                    @endforeach
                    
                </div>
            @endif
        </section>
    </main>

    <footer class="border-t border-garis py-10 mt-12 bg-permukaan/30">
        <div class="max-w-[1200px] mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4 text-[13px] text-redup">
            <div>
                &copy; {{ date('Y') }} Mitra Anda Seponti. All rights reserved.
            </div>
            <div class="font-serif text-lg text-teks">
                Buku Kas & Inventaris
            </div>
        </div>
    </footer>

    <!-- SCRIPT SLIDER VANILLA JS (Gaya Tokopedia) -->
    <script>
        const track = document.getElementById('slider-track');
        const dots = document.querySelectorAll('.dot');
        const totalSlides = 3;
        let currentSlide = 0;
        let slideInterval;

        function updateSlider() {
            // Geser gambar
            track.style.transform = `translateX(-${currentSlide * 100}%)`;
            
            // Perbarui titik indikator (dots)
            dots.forEach((dot, index) => {
                if (index === currentSlide) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider();
            resetInterval();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlider();
            resetInterval();
        }
        
        function goToSlide(index) {
            currentSlide = index;
            updateSlider();
            resetInterval();
        }

        function resetInterval() {
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 5000); // Geser tiap 5 detik
        }

        // Jalankan saat pertama kali dimuat
        resetInterval();
    </script>

</body>
</html>