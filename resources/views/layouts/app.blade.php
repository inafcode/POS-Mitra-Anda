<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Buku Kas') — Mitra Anda</title>
    
    <!-- Tipografi -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;500&family=Inter:wght@400;500&display=swap" rel="stylesheet">

    <style>
        /* Design Tokens */
        :root {
            --latar: #F5EFE6;
            --teks: #292929;
            --teks-redup: #6B6257;
            --garis: #DCD2C2;
            --permukaan: #EDE5D8;
            --aksen: #B8532F;
            --teks-aksen: #FBF7F0;

            --font-judul: 'Fraunces', serif;
            --font-isi: 'Inter', sans-serif;
        }

        /* Reset & Base */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: var(--latar);
            color: var(--teks);
            font-family: var(--font-isi);
            font-size: 17px;
            line-height: 1.65;
            -webkit-font-smoothing: antialiased;
        }
        
        h1, h2, h3, .font-judul {
            font-family: var(--font-judul);
            font-weight: 500;
            line-height: 1.15;
            color: var(--teks);
        }
        .teks-redup { color: var(--teks-redup); }
        .teks-kecil { font-size: 13px; line-height: 1.4; }
        .angka { font-variant-numeric: tabular-nums; }
        .rata-kanan { text-align: right; }

        /* Interaksi & Aksesibilitas */
        a, button, input, select { transition: all 120ms ease-out; }
        :focus-visible {
            outline: 2px solid var(--teks);
            outline-offset: 2px;
        }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }

        /* Layout Utama */
        .layout-app { display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar {
            width: 220px;
            flex-shrink: 0;
            border-right: 1px solid var(--garis);
            display: flex;
            flex-direction: column;
            padding: 40px 24px;
        }
        .sidebar-brand {
            font-family: var(--font-judul);
            font-size: 22px;
            text-decoration: none;
            color: var(--teks);
            margin-bottom: 64px;
        }
        .nav-list { list-style: none; display: flex; flex-direction: column; gap: 16px; flex: 1; }
        .nav-item {
            text-decoration: none;
            color: var(--teks-redup);
            font-size: 15px;
            padding: 4px 0 4px 16px;
            border-left: 2px solid transparent;
        }
        .nav-item:hover { color: var(--teks); }
        .nav-item.aktif {
            color: var(--teks);
            font-weight: 500;
            border-left-color: var(--teks);
        }
        .sidebar-footer { font-size: 13px; color: var(--teks-redup); border-top: 1px solid var(--garis); padding-top: 24px; }
        .sidebar-footer strong { color: var(--teks); font-weight: 500; display: block; margin-bottom: 4px; }

        /* Konten */
        .konten {
            flex: 1;
            padding: 48px 80px;
            max-width: 1600px; /* Memberi ruang lebih lebar dan proporsional */
            margin: 0 auto;    /* Menjaga posisi konten tetap simetris di tengah */
            overflow-y: auto;
        }

        .header-halaman {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 64px;
            border-bottom: 1px solid var(--garis);
            padding-bottom: 24px;
        }
        .header-halaman h1 { font-size: 40px; margin-bottom: 8px; }

        /* Tombol & Komponen */
        .btn-utama {
            background-color: var(--aksen);
            color: var(--teks-aksen);
            border: none;
            border-radius: 4px;
            padding: 10px 24px;
            font-family: var(--font-isi);
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-utama:hover { background-color: #9A4325; }
        
        .btn-sekunder {
            background-color: transparent;
            color: var(--teks);
            border: 1px solid var(--garis);
            border-radius: 4px;
            padding: 6px 16px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-sekunder:hover { background-color: var(--permukaan); }

        /* Status & Badge */
        .status-grup { display: inline-flex; align-items: center; gap: 8px; font-size: 13px; }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; }
        .status-proses { color: #A87B2A; } .status-proses .status-dot { background-color: #A87B2A; }
        .status-kirim { color: #7A6F64; } .status-kirim .status-dot { background-color: #7A6F64; }
        .status-selesai { color: #5E6B45; } .status-selesai .status-dot { background-color: #5E6B45; }
        .status-batal { color: var(--teks-redup); text-decoration: line-through; }

        /* Tabel Polos */
        .tabel-data { width: 100%; border-collapse: collapse; font-size: 15px; }
        .tabel-data th { text-align: left; font-weight: 400; color: var(--teks-redup); border-bottom: 1px solid var(--garis); padding: 12px 16px; font-size: 13px; }
        .tabel-data td { border-bottom: 1px solid var(--garis); padding: 16px; }
        .tabel-data tbody tr:hover { background-color: var(--permukaan); }

        /* States */
        .state-kosong { padding: 40px 0; font-size: 15px; color: var(--teks-redup); }
        .state-error { padding: 24px; border: 1px solid var(--garis); background-color: var(--permukaan); font-size: 15px; margin-bottom: 24px; }
        
        @media (max-width: 1099px) {
            .layout-app { flex-direction: column; }
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--garis); padding: 24px; flex-direction: row; align-items: center; justify-content: space-between; }
            .sidebar-brand { margin-bottom: 0; }
            .nav-list { flex-direction: row; gap: 24px; }
            .nav-item { border-left: none; border-bottom: 2px solid transparent; padding: 4px 0; }
            .nav-item.aktif { border-left: none; border-bottom-color: var(--teks); }
            .sidebar-footer { display: none; }
            .konten { padding: 32px 24px; }
        }
    </style>
</head>
<body>
    <div class="layout-app">
        <!-- Sidebar -->
        <aside class="sidebar">
            <a href="/admin" class="sidebar-brand">Mitra Anda</a>
            <ul class="nav-list">
                <li>
                    <a href="/admin" class="nav-item {{ request()->is('admin') && !request()->is('admin/*') ? 'aktif' : '' }}">Ringkasan</a>
                </li>
                <li>
                    <a href="/admin/produk" class="nav-item {{ request()->is('admin/produk*') || request()->is('admin/create') || request()->is('admin/*/edit') ? 'aktif' : '' }}">Katalog Barang</a>
                </li>
                <li>
                    <a href="/kasir" class="nav-item {{ request()->is('kasir*') ? 'aktif' : '' }}">Kasir POS</a>
                </li>
                <li>
                    <a href="/admin/instansi" class="nav-item {{ request()->is('admin/instansi*') ? 'aktif' : '' }}">Mitra Instansi</a>
                </li>
                <li>
                    <a href="/laporan" class="nav-item {{ request()->is('laporan*') ? 'aktif' : '' }}">Buku Kas</a>
                </li>
                <li>
                    <a href="/admin/pesanan" class="nav-item {{ request()->is('admin/pesanan*') ? 'aktif' : '' }}">Pesanan Instansi</a>
                </li>
                <li>
                    <a href="/" style="display: block; padding: 12px 16px; color: var(--teks-redup); text-decoration: none; font-size: 14px; font-weight: 500; border-top: 1px solid var(--garis); margin-top: 16px;">
                        Lihat Katalog Publik
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <strong>{{ Auth::user()->name ?? 'Pemilik Toko' }}</strong>
                <form action="/logout" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:var(--teks-redup);text-decoration:underline;cursor:pointer;font-family:inherit;font-size:13px;">Keluar</button>
                </form>
            </div>
        </aside>

        <!-- Konten Utama -->
        <main class="konten">
            @yield('content')
        </main>
    </div>

    <!-- Helper Global Berdasarkan Instruksi -->
    <script>
        const formatUang = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
        const formatTanggal = new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        
        // Render tanggal hari ini ke elemen dengan ID 'tanggal-hari-ini' jika ada
        const elTanggal = document.getElementById('tanggal-hari-ini');
        if (elTanggal) elTanggal.textContent = formatTanggal.format(new Date());
    </script>
    @yield('scripts')
</body>
</html>