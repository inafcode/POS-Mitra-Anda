@extends('layouts.app')

@section('title', 'Ringkasan')

@section('content')
<style>
    .metrik-utama { display: flex; align-items: baseline; gap: 64px; margin-bottom: 64px; }
    .hero-kiri { flex-shrink: 0; }
    .hero-angka { font-size: 72px; margin: 8px 0; line-height: 1; }
    .hero-kanan { display: flex; gap: 40px; border-left: 1px solid var(--garis); padding-left: 64px; flex: 1; }
    .metrik-item { flex: 1; }
    .metrik-angka { font-size: 28px; margin: 4px 0; }

    .seksi-pemisah { border-top: 1px solid var(--garis); padding-top: 48px; margin-top: 48px; }
    .header-seksi { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 24px; }
    .opsi-grafik { border: none; background: none; color: var(--teks-redup); cursor: pointer; padding-bottom: 2px; font-size: 13px; margin-left: 16px; }
    .opsi-grafik.aktif { color: var(--teks); font-weight: 500; border-bottom: 1px solid var(--teks); }

    .grid-asimetris { display: grid; grid-template-columns: 2fr 1fr; gap: 64px; }
    .bar-stok { height: 3px; background-color: var(--permukaan); width: 100%; margin-top: 8px; }
    .bar-stok-isi { height: 100%; background-color: var(--teks); }
    
    .list-terlaris { list-style: none; margin-top: 24px; }
    .item-terlaris { display: flex; align-items: baseline; border-bottom: 1px solid var(--garis); padding: 12px 0; }
    .nomor-urut { font-family: var(--font-judul); font-size: 20px; color: var(--teks-redup); width: 32px; flex-shrink: 0; }
</style>

<div class="header-halaman">
    <div>
        <h1>Ringkasan</h1>
        <p class="teks-redup" id="tanggal-hari-ini">Memuat tanggal...</p>
    </div>
    <a href="/kasir" class="btn-utama">Buka Kasir</a>
</div>

<!-- Angka Utama -->
<div class="metrik-utama">
    <div class="hero-kiri">
        <div class="teks-kecil teks-redup">Pendapatan hari ini</div>
        <div class="hero-angka angka font-judul">Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}</div>
        <div class="teks-kecil teks-redup">transaksi tunai & transfer lunas</div>
    </div>
    <div class="hero-kanan">
        <div class="metrik-item">
            <div class="teks-kecil teks-redup">Perlu diproses</div>
            <div class="metrik-angka angka font-judul">{{ $totalPerluDiproses ?? 0 }}</div>
            <div class="teks-kecil teks-redup">pesanan B2B</div>
        </div>
        <div class="metrik-item">
            <div class="teks-kecil teks-redup">Stok menipis</div>
            <div class="metrik-angka angka font-judul">{{ $totalStokMenipis ?? 0 }}</div>
            <div class="teks-kecil teks-redup">barang</div>
        </div>
        <div class="metrik-item">
            <div class="teks-kecil teks-redup">Piutang berjalan</div>
            <div class="metrik-angka angka font-judul">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</div>
            <div class="teks-kecil teks-redup">dari instansi</div>
        </div>
    </div>
</div>

<!-- Grafik Penjualan (SVG Inline Murni) -->
<div class="seksi-pemisah">
    <div class="header-seksi">
        <h2 style="font-size: 24px;">Penjualan</h2>
        <div>
            <button class="opsi-grafik aktif">7H</button>
            <button class="opsi-grafik">30H</button>
            <button class="opsi-grafik">90H</button>
        </div>
    </div>
    <svg viewBox="0 0 1000 180" style="width: 100%; height: 180px; overflow: visible;">
        <line x1="0" y1="20" x2="1000" y2="20" stroke="var(--garis)" stroke-width="1" stroke-dasharray="4 4" />
        <line x1="0" y1="90" x2="1000" y2="90" stroke="var(--garis)" stroke-width="1" stroke-dasharray="4 4" />
        <line x1="0" y1="160" x2="1000" y2="160" stroke="var(--garis)" stroke-width="1" />
        
        <path d="M 0 120 L 160 140 L 330 80 L 500 110 L 660 40 L 830 70 L 1000 30" 
              fill="none" stroke="var(--teks)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              
        <circle cx="660" cy="40" r="3.5" fill="var(--teks)" />
        <text x="660" y="24" font-family="var(--font-isi)" font-size="12" fill="var(--teks-redup)" text-anchor="middle" class="angka">Puncak</text>
        
        <circle cx="1000" cy="30" r="3.5" fill="var(--teks)" />
        <text x="1000" y="14" font-family="var(--font-isi)" font-size="12" fill="var(--teks)" text-anchor="end" class="angka">Hari ini</text>
    </svg>
</div>

<!-- Kolom 2:1 -->
<div class="seksi-pemisah grid-asimetris">
    <!-- Tabel Pesanan -->
    <div>
        <div class="header-seksi" style="margin-bottom: 16px;">
            <h2 style="font-size: 20px;">Pesanan yang perlu diproses</h2>
        </div>
        <table class="tabel-data">
            <thead>
                <tr>
                    <th>Nota</th>
                    <th>Instansi</th>
                    <th>Status</th>
                    <th class="rata-kanan">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesananPerluDiproses ?? [] as $pesanan)
                <tr>
                    <td class="angka">{{ $pesanan->kode_nota }}</td>
                    <td>{{ $pesanan->instansi->name ?? 'Umum' }}</td>
                    <td>
                        <span class="status-grup status-proses"><span class="status-dot"></span>Perlu diproses</span>
                    </td>
                    <td class="rata-kanan angka">Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4"><div class="state-kosong">Tidak ada pesanan yang perlu diproses hari ini.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Stok Menipis & Terlaris -->
    <div>
        <div class="header-seksi" style="margin-bottom: 16px;">
            <h2 style="font-size: 20px;">Stok menipis</h2>
        </div>
        <div style="display: flex; flex-direction: column; gap: 24px;">
            @forelse($stokMenipis ?? [] as $stok)
            <div>
                <div style="display: flex; justify-content: space-between; font-size: 15px; margin-bottom: 4px;">
                    <span>{{ $stok->nama_item }}</span>
                    <span class="angka teks-redup">{{ $stok->stok }} {{ $stok->satuan }}</span>
                </div>
                <div class="bar-stok">
                    <div class="bar-stok-isi" style="width: {{ min(100, ($stok->stok / 10) * 100) }}%;"></div>
                </div>
            </div>
            @empty
            <div class="state-kosong" style="padding: 16px 0;">Semua stok dalam batas aman.</div>
            @endforelse
        </div>

        <h3 style="font-size: 17px; margin-top: 64px; border-top: 1px solid var(--garis); padding-top: 24px;">Terlaris minggu ini</h3>
        <ul class="list-terlaris">
            @forelse($produkTerlaris ?? [] as $index => $laris)
            <li class="item-terlaris">
                <span class="nomor-urut">{{ $index + 1 }}</span>
                <span style="flex: 1; font-size: 15px;">{{ $laris->nama_item }}</span>
                <span class="angka rata-kanan" style="font-size: 15px;">{{ $laris->total_terjual ?? 0 }}</span>
            </li>
            @empty
            <li class="teks-kecil teks-redup" style="padding: 16px 0;">Belum ada data penjualan minggu ini.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection