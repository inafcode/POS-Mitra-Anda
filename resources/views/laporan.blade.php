@extends('layouts.app')

@section('title', 'Buku Kas & Cashflow')

@section('content')
<style>
    .seksi-laporan {
        margin-top: 48px;
        padding-top: 40px;
        border-top: 1px solid var(--garis);
    }

    .judul-bagian {
        font-size: 20px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: baseline;
    }

    /* Pembeda kepemilikan entitas: Tenang & Nostalgik */
    .baris-kado {
        background-color: rgba(237, 229, 216, 0.45);
    }
    .baris-kado:hover {
        background-color: var(--permukaan) !important;
    }
    
    .tag-entitas {
        display: inline-block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 500;
        color: var(--teks-redup);
    }
    .tag-entitas.kado {
        color: var(--teks);
        font-weight: 600;
    }

    /* Input form angka di dalam tabel */
    .input-angka-tabel {
        background: transparent;
        border: 1px solid var(--garis);
        border-radius: 2px;
        font-family: inherit;
        font-size: 15px;
        padding: 4px 8px;
        color: var(--teks);
    }
    .input-angka-tabel:focus {
        outline: 2px solid var(--teks);
        outline-offset: 1px;
        background: #FDFBF7;
    }

    /* Layout 3 Bagian Bawah */
    .grid-buku-bawah {
        display: grid;
        grid-template-columns: 1.2fr 1fr 1fr;
        gap: 32px;
        align-items: start;
    }

    .kotak-kolom {
        border-top: 2px solid var(--teks);
        padding-top: 16px;
    }

    .kotak-kolom-judul {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 16px;
        color: var(--teks);
        letter-spacing: 0.02em;
    }

    .tabel-padat {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    .tabel-padat td {
        padding: 10px 0;
        border-bottom: 1px solid var(--garis);
        vertical-align: middle;
    }

    /* Tombol Aksi Minimalis */
    .btn-batal-teks {
        background: none;
        border: none;
        color: var(--teks-redup);
        cursor: pointer;
        font-size: 13px;
        text-decoration: underline;
        font-family: inherit;
        padding: 0;
    }
    .btn-batal-teks:hover {
        color: var(--aksen);
    }

    .info-rekening {
        padding: 16px 20px;
        background-color: var(--permukaan);
        border-radius: 4px;
        font-size: 13px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }

    @media (max-width: 1024px) {
        .grid-buku-bawah {
            grid-template-columns: 1fr;
            gap: 40px;
        }
    }
</style>

<!-- Header Halaman & Filter Tanggal -->
<div class="header-halaman">
    <div>
        <h1>Buku Kas & Cashflow</h1>
        <p class="teks-redup teks-kecil">
            Catatan penerimaan kas laci dan mutasi harian toko
        </p>
    </div>

    <div style="display: flex; align-items: center; gap: 16px;">
        <form action="/laporan" method="GET" style="display: flex; align-items: center; gap: 8px;">
            <label for="input-tanggal" class="teks-kecil teks-redup">Tanggal:</label>
            <input type="date" id="input-tanggal" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="input-angka-tabel" style="padding: 6px 12px; font-weight: 500;">
        </form>

        <a href="/laporan/export?tanggal={{ $tanggal }}" class="btn-sekunder">Ekspor Catatan</a>
        <a href="/kasir" class="btn-sekunder">Buka Kasir</a>
    </div>
</div>

@if(session('success'))
<div style="padding: 14px 20px; border-left: 2px solid var(--teks); background-color: var(--permukaan); font-size: 14px; margin-bottom: 32px;">
    {{ session('success') }}
</div>
@endif

<!-- Info Non-Tunai Rekening -->
@if(($nonTunaiKado ?? 0) > 0 || ($nonTunaiMitra ?? 0) > 0)
<div class="info-rekening">
    <span class="teks-redup">Penerimaan Non-Tunai (QRIS & Transfer langsung ke rekening bank, tidak masuk laci kasir):</span>
    <div style="display: flex; gap: 24px;" class="angka">
        <span>Kado Koeni: <strong>Rp {{ number_format($nonTunaiKado ?? 0, 0, ',', '.') }}</strong></span>
        <span style="color: var(--garis);">|</span>
        <span>Mitra Anda: <strong>Rp {{ number_format($nonTunaiMitra ?? 0, 0, ',', '.') }}</strong></span>
    </div>
</div>
@endif

<!-- TABEL 1: RINCIAN PENJUALAN BARANG -->
<section>
    <div class="judul-bagian">
        <h2>Rincian Barang Terjual</h2>
        <span class="teks-kecil teks-redup">Baris berwarna menandai kepemilikan Kado Koeni</span>
    </div>

    <div class="tabel-wadah">
        <table class="tabel-data">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th style="width: 140px;">Nota</th>
                    <th>Nama Barang</th>
                    <th style="width: 110px;">Pemilik</th>
                    <th style="width: 60px; text-align: center;">Qty</th>
                    <th style="width: 70px; text-align: center;">Satuan</th>
                    <th class="rata-kanan" style="width: 120px;">Harga</th>
                    <th class="rata-kanan" style="width: 130px;">Subtotal</th>
                    <th style="width: 100px; text-align: center;">Waktu</th>
                    <th style="width: 80px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $d)
                <tr class="{{ $d->pemilik == 'Kado Koeni' ? 'baris-kado' : '' }}">
                    <td class="angka teks-redup teks-kecil">#{{ $d->id + 8000 }}</td>
                    <td>
                        <span class="angka" style="font-weight: 500;">{{ $d->transaction->kode_nota ?? '-' }}</span>
                        @if(($d->transaction->metode_pembayaran ?? 'Tunai') !== 'Tunai')
                            <div class="teks-kecil teks-redup">{{ strtoupper($d->transaction->metode_pembayaran) }}</div>
                        @endif
                    </td>
                    <td style="font-weight: 500;">{{ $d->nama_item }}</td>
                    <td>
                        <span class="tag-entitas {{ $d->pemilik == 'Kado Koeni' ? 'kado' : '' }}">{{ $d->pemilik }}</span>
                    </td>
                    <td class="angka" style="text-align: center;">{{ $d->qty }}</td>
                    <td class="teks-redup teks-kecil" style="text-align: center;">{{ $d->satuan ?? 'pcs' }}</td>
                    <td class="rata-kanan angka">Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                    <td class="rata-kanan angka" style="font-weight: 500;">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    <td class="angka teks-kecil teks-redup" style="text-align: center;">{{ $d->created_at->format('H:i') }}</td>
                    <td style="text-align: center;">
                        <form action="/laporan/detail/{{ $d->id }}" method="POST" onsubmit="return confirm('Batalkan transaksi {{ addslashes($d->nama_item) }}? Stok akan dikembalikan ke gudang.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-batal-teks">Batalkan</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="state-kosong">Belum ada barang terjual pada tanggal ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<!-- FORM REKAP CASHFLOW & BAGI HASIL -->
<form action="/laporan/cashflow" method="POST" class="seksi-laporan">
    @csrf
    <input type="hidden" name="tanggal" value="{{ $tanggal }}">

    <!-- TABEL 2: REKAP MODAL & KAS LACI -->
    <div class="judul-bagian">
        <h2>Rekapitulasi Kas Laci</h2>
        <span class="teks-kecil teks-redup">Dihitung dari penerimaan fisik tunai</span>
    </div>

    <div class="tabel-wadah" style="margin-bottom: 48px;">
        <table class="tabel-data">
            <thead>
                <tr>
                    <th style="width: 220px;">Unit Toko</th>
                    <th class="rata-kanan" style="width: 220px;">Modal Awal (Sisa Kemarin)</th>
                    <th class="rata-kanan">Pemasukan Kas</th>
                    <th class="rata-kanan">Beban Pengeluaran</th>
                    <th class="rata-kanan" style="width: 200px;">Total Fisik Laci</th>
                </tr>
            </thead>
            <tbody>
                <!-- Kado Koeni -->
                <tr>
                    <td>
                        <strong style="font-weight: 500;">Kado Koeni</strong>
                    </td>
                    <td class="rata-kanan">
                        <span class="teks-redup teks-kecil">Rp</span>
                        <input type="number" name="modal_awal_kado" value="{{ $cashflow->modal_awal_kado }}" class="input-angka-tabel angka rata-kanan" style="width: 140px; font-weight: 500;">
                    </td>
                    <td class="rata-kanan angka">Rp {{ number_format($pemasukanKado, 0, ',', '.') }}</td>
                    <td class="rata-kanan angka teks-redup">
                        {{ $pengeluaranKado > 0 ? 'Rp ' . number_format($pengeluaranKado, 0, ',', '.') : '—' }}
                    </td>
                    <td class="rata-kanan angka" style="font-weight: 500;">Rp {{ number_format($totalKado, 0, ',', '.') }}</td>
                </tr>

                <!-- Mitra Anda -->
                <tr>
                    <td>
                        <strong style="font-weight: 500;">Mitra Anda</strong>
                    </td>
                    <td class="rata-kanan">
                        <span class="teks-redup teks-kecil">Rp</span>
                        <input type="number" name="modal_awal_mitra" value="{{ $cashflow->modal_awal_mitra }}" class="input-angka-tabel angka rata-kanan" style="width: 140px; font-weight: 500;">
                    </td>
                    <td class="rata-kanan angka">Rp {{ number_format($pemasukanMitra, 0, ',', '.') }}</td>
                    <td class="rata-kanan angka teks-redup">
                        {{ $pengeluaranMitra > 0 ? 'Rp ' . number_format($pengeluaranMitra, 0, ',', '.') : '—' }}
                    </td>
                    <td class="rata-kanan angka" style="font-weight: 500;">Rp {{ number_format($totalMitra, 0, ',', '.') }}</td>
                </tr>

                <!-- Grand Total Laci -->
                <tr style="border-top: 2px solid var(--teks);">
                    <td colspan="4" style="font-weight: 500; font-size: 15px; padding-top: 20px;">
                        Total Kas Laci Keseluruhan
                    </td>
                    <td class="rata-kanan angka font-judul" style="font-size: 24px; padding-top: 20px;">
                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- 3 BAGIAN: BUKU PENGELUARAN, SETORAN HASIL, MODAL BESOK -->
    <div class="grid-buku-bawah">
        
        <!-- Kolom 1: Daftar Catatan Pengeluaran Hari Ini -->
        <div class="kotak-kolom">
            <div class="kotak-kolom-judul">Catatan Pengeluaran</div>
            <table class="tabel-padat">
                <tbody>
                    @forelse($expenses as $exp)
                    <tr>
                        <td style="width: 80px;" class="teks-kecil teks-redup">{{ $exp->pemilik }}</td>
                        <td>{{ $exp->keterangan }}</td>
                        <td class="rata-kanan angka" style="width: 90px;">Rp {{ number_format($exp->nominal, 0, ',', '.') }}</td>
                        <td style="width: 30px; text-align: right;">
                            <button type="button" onclick="hapusPengeluaran({{ $exp->id }})" class="btn-batal-teks" title="Hapus baris pengeluaran">✕</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="state-kosong" style="padding: 16px 0; text-align: left;">Tidak ada pengeluaran laci hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Kolom 2: Setoran Bersih yang Diambil -->
        <div class="kotak-kolom">
            <div class="kotak-kolom-judul">Setoran / Bagi Hasil</div>
            <table class="tabel-padat">
                <tbody>
                    <tr>
                        <td>Kado Koeni</td>
                        <td class="rata-kanan">
                            <span class="teks-redup teks-kecil">Rp</span>
                            <input type="number" name="hasil_diambil_kado" value="{{ $cashflow->hasil_diambil_kado }}" class="input-angka-tabel angka rata-kanan" style="width: 130px; font-weight: 500;">
                        </td>
                    </tr>
                    <tr>
                        <td>Mitra Anda</td>
                        <td class="rata-kanan">
                            <span class="teks-redup teks-kecil">Rp</span>
                            <input type="number" name="hasil_diambil_mitra" value="{{ $cashflow->hasil_diambil_mitra }}" class="input-angka-tabel angka rata-kanan" style="width: 130px; font-weight: 500;">
                        </td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 24px;">
                <!-- SATU-SATUNYA TOMBOL AKSEN TERAKOTA PADA HALAMAN INI -->
                <button type="submit" class="btn-utama" style="width: 100%; justify-content: center; font-size: 14px; padding: 12px 16px;">
                    Simpan Buku Kas & Bagi Hasil
                </button>
            </div>
        </div>

        <!-- Kolom 3: Sisa Modal Laci untuk Besok -->
        <div class="kotak-kolom">
            <div class="kotak-kolom-judul">Sisa Kas Laci Menginap</div>
            <table class="tabel-padat">
                <tbody>
                    <tr>
                        <td class="teks-redup">Kado Koeni</td>
                        <td class="rata-kanan angka" style="font-weight: 500;">Rp {{ number_format($sisaModalKado, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="teks-redup">Mitra Anda</td>
                        <td class="rata-kanan angka" style="font-weight: 500;">Rp {{ number_format($sisaModalMitra, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-top: 1px solid var(--teks);">
                        <td style="font-weight: 500; padding-top: 14px;">Total Menginap</td>
                        <td class="rata-kanan angka font-judul" style="font-size: 18px; padding-top: 14px;">
                            Rp {{ number_format($totalSisaModal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</form>

<!-- FORM TAMBAH PENGELUARAN BARU -->
<section class="seksi-laporan" style="margin-bottom: 64px;">
    <div class="judul-bagian">
        <h2>Catat Pengeluaran Baru</h2>
        <span class="teks-kecil teks-redup">Pengeluaran operasional yang diambil langsung dari uang laci</span>
    </div>

    <form action="/laporan/pengeluaran" method="POST" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
        @csrf
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        
        <select name="pemilik" class="input-angka-tabel" style="padding: 8px 12px; font-size: 14px;">
            <option value="Kado Koeni">Beban Kado Koeni</option>
            <option value="Mitra Anda">Beban Mitra Anda</option>
        </select>

        <input type="text" name="keterangan" placeholder="Keterangan pengeluaran (misal: Laundry, Plastik)" required class="input-angka-tabel" style="flex: 1; min-width: 260px; padding: 8px 12px;">

        <input type="number" name="nominal" placeholder="Nominal (Rp)" required class="input-angka-tabel angka rata-kanan" style="width: 160px; padding: 8px 12px;">

        <button type="submit" class="btn-sekunder" style="padding: 9px 20px; font-size: 14px;">
            Catat Pengeluaran
        </button>
    </form>
</section>

<!-- Form Tersembunyi untuk Penghapusan Baris Pengeluaran via JS -->
<form id="form-hapus-exp" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    function hapusPengeluaran(id) {
        if (confirm('Hapus catatan pengeluaran ini dari buku kas?')) {
            const form = document.getElementById('form-hapus-exp');
            form.action = '/laporan/pengeluaran/' + id;
            form.submit();
        }
    }
</script>
@endsection