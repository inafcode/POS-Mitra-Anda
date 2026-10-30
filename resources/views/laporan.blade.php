@extends('layouts.app')

@section('title', 'Buku Kas & Cashflow')

@section('content')
<style>
    /* Tipografi & Warna Dasar */
    .judul-halaman { font-family: 'Fraunces', serif; font-size: 28px; color: var(--teks); }
    .teks-redup { color: var(--teks-redup); }
    
    /* Tabel ala Excel / Folio */
    .tabel-excel {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        background-color: #FAFAFA;
        border: 2px solid var(--teks);
        margin-bottom: 24px;
    }
    .tabel-excel th, .tabel-excel td {
        border: 1px solid var(--garis);
        padding: 10px 16px;
        vertical-align: middle;
    }
    .tabel-excel th {
        background-color: var(--permukaan);
        color: var(--teks);
        font-weight: 600;
        text-align: center;
        border-bottom: 2px solid var(--teks);
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.05em;
    }
    
    /* Baris Kategori Utama (A & B) */
    .baris-kategori td {
        background-color: var(--permukaan);
        font-weight: 600;
        font-size: 14px;
        border-top: 2px solid var(--teks);
        border-bottom: 1px solid var(--teks);
    }
    
    /* Baris Kesimpulan (Total) */
    .baris-total td {
        background-color: #F0EBE1;
        font-weight: 600;
        border-top: 1px solid var(--teks);
        border-bottom: 2px solid var(--teks);
    }

    .tabel-excel td.keterangan { font-weight: 500; }
    .tabel-excel td.angka { text-align: right; font-variant-numeric: tabular-nums; }
    
    /* Input di dalam sel Excel */
    .input-sel {
        width: 100%;
        background-color: #FFFFFF;
        border: 1px dashed var(--teks-redup);
        border-radius: 2px;
        font-family: inherit;
        font-size: 15px;
        padding: 4px 8px;
        color: var(--teks);
        text-align: right;
        font-weight: 600;
    }
    .input-sel:focus {
        outline: 2px solid var(--aksen);
        outline-offset: -1px;
        border-color: transparent;
        background-color: #FFFDF5;
    }

    /* Tabel Rincian Atas (Polos) */
    .tabel-polos { width: 100%; border-collapse: collapse; font-size: 13px; }
    .tabel-polos th, .tabel-polos td { padding: 8px 12px; border-bottom: 1px solid var(--garis); }
    .tabel-polos th { text-align: left; color: var(--teks-redup); font-weight: 500; }
    .tabel-polos td.angka { text-align: right; }
    .baris-kado { background-color: rgba(237, 229, 216, 0.45); }

    /* Tombol-tombol */
    .btn-utama {
        background-color: var(--aksen); color: #FBF7F0; font-weight: 500; font-size: 15px;
        padding: 12px 24px; border-radius: 4px; border: none; cursor: pointer;
        display: inline-flex; justify-content: center; width: 100%; transition: 150ms;
    }
    .btn-utama:hover { opacity: 0.9; }
    .btn-sekunder {
        background: transparent; border: 1px solid var(--garis); border-radius: 4px;
        padding: 6px 16px; font-size: 13px; color: var(--teks); font-weight: 500;
    }
    .btn-batal-teks {
        background: none; border: none; color: var(--teks-redup); cursor: pointer;
        font-size: 12px; text-decoration: underline;
    }
</style>

<!-- Header & Tanggal -->
<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid var(--teks);">
    <div>
        <h1 class="judul-halaman">Buku Folio Kasir</h1>
        <p class="teks-redup teks-kecil mt-1">Rekapitulasi penjualan, kas fisik laci, dan bagi hasil dompet</p>
    </div>
    <div style="display: flex; align-items: center; gap: 16px;">
        <form action="/laporan" method="GET" style="display: flex; align-items: center; gap: 8px;">
            <label class="teks-kecil teks-redup">Pilih Tanggal:</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="input-sel" style="width: auto; background: transparent; border: 1px solid var(--garis);">
        </form>
        <a href="/laporan/export?tanggal={{ $tanggal }}" class="btn-sekunder" style="background: var(--permukaan);">Cetak Excel</a>
    </div>
</div>

@if(session('success'))
<div style="padding: 12px 16px; border-left: 4px solid var(--teks); background-color: var(--permukaan); font-size: 14px; font-weight: 500; margin-bottom: 24px;">
    {{ session('success') }}
</div>
@endif

<!-- ==========================================
     BAGIAN 1: RINCIAN JUAL & PENGELUARAN (ATAS)
     ========================================== -->
<div style="display: grid; grid-template-columns: 2fr 1.2fr; gap: 32px; margin-bottom: 48px;">
    
    <!-- Kiri: Penjualan -->
    <div>
        <div style="font-weight: 600; margin-bottom: 12px; font-size: 16px;">1. Rincian Penjualan Barang & Jasa</div>
        <div style="border: 1px solid var(--garis); max-height: 300px; overflow-y: auto; background: #fff;">
            <table class="tabel-polos">
                <thead>
                    <tr style="background: var(--permukaan);">
                        <th>Nota / Jam</th>
                        <th>Keterangan Item</th>
                        <th>Mitra/Kado</th>
                        <th class="angka">Subtotal</th>
                        <th style="text-align: center;">Batal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $d)
                    <tr class="{{ $d->pemilik == 'Kado Koeni' ? 'baris-kado' : '' }}">
                        <td>
                            <strong>{{ $d->transaction->kode_nota ?? '-' }}</strong><br>
                            <span class="teks-redup" style="font-size: 11px;">{{ $d->created_at->format('H:i') }}</span>
                        </td>
                        <td>{{ $d->nama_item }} <span class="teks-redup" style="font-size: 11px;">({{ $d->qty }}x)</span></td>
                        <td>{{ $d->pemilik }}</td>
                        <td class="angka font-medium">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        <td style="text-align: center;">
                            <form action="/laporan/detail/{{ $d->id }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-batal-teks">✕</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align: center; padding: 24px; color: var(--teks-redup);">Belum ada penjualan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kanan: Pengeluaran & Input Pengeluaran -->
    <div>
        <div style="font-weight: 600; margin-bottom: 12px; font-size: 16px;">2. Catatan Pengeluaran Toko</div>
        
        <!-- Input Cepat Pengeluaran -->
        <form action="/laporan/pengeluaran" method="POST" style="display: flex; gap: 8px; margin-bottom: 16px;">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <select name="pemilik" style="padding: 6px; border: 1px solid var(--garis); width: 80px; font-size: 12px;">
                <option value="Kado Koeni">Kado</option>
                <option value="Mitra Anda">Mitra</option>
            </select>
            <input type="text" name="keterangan" placeholder="Beli plastik..." required style="flex: 1; padding: 6px; border: 1px solid var(--garis); font-size: 12px;">
            <input type="number" name="nominal" placeholder="Rp..." required style="width: 80px; padding: 6px; border: 1px solid var(--garis); font-size: 12px;">
            <button type="submit" class="btn-sekunder" style="padding: 6px 12px;">+</button>
        </form>

        <div style="border: 1px solid var(--garis); max-height: 245px; overflow-y: auto; background: #fff;">
            <table class="tabel-polos">
                <tbody>
                    @forelse($expenses as $exp)
                    <tr>
                        <td>
                            <strong>{{ $exp->keterangan }}</strong><br>
                            <span class="teks-redup" style="font-size: 11px;">Beban: {{ $exp->pemilik }}</span>
                        </td>
                        <td class="angka" style="color: var(--aksen); font-weight: 500;">
                            -Rp {{ number_format($exp->nominal, 0, ',', '.') }}
                        </td>
                        <td style="width: 30px; text-align: center;">
                            <button type="button" onclick="hapusPengeluaran({{ $exp->id }})" class="btn-batal-teks">✕</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align: center; padding: 24px; color: var(--teks-redup);">Tidak ada pengeluaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==========================================
     BAGIAN 2: KERTAS KERJA EXCEL (KAS & DOMPET)
     ========================================== -->
<div style="font-weight: 600; margin-bottom: 12px; font-size: 16px;">3. Kertas Kerja Rekapitulasi (Buku Kas Induk)</div>

<form action="/laporan/cashflow" method="POST">
    @csrf
    <input type="hidden" name="tanggal" value="{{ $tanggal }}">

    <table class="tabel-excel">
        <thead>
            <tr>
                <th style="width: 35%; text-align: left; padding-left: 20px;">Uraian Arus Kas</th>
                <th style="width: 20%;">Kado Koeni</th>
                <th style="width: 20%;">Mitra Anda</th>
                <th style="width: 25%; background-color: #EAE6DB;">TOTAL LACI FISIK</th>
            </tr>
        </thead>
        <tbody>
            
            <!-- BAGIAN A: LACI FISIK -->
            <tr class="baris-kategori">
                <td colspan="4" style="padding-left: 20px;">A. PERPUTARAN LACI KASIR (UANG BERSAMA)</td>
            </tr>
            <tr>
                <td class="keterangan" style="padding-left: 24px;">
                    1. Modal Awal / Sisa Laci H-1
                    @php
                        $tglKemarin = \Carbon\Carbon::parse($tanggal)->subDay()->toDateString();
                        $cfH1 = \App\Models\DailyCashflow::where('tanggal', $tglKemarin)->first();
                        $sisaH1Kado = $cfH1 ? ($cfH1->modal_awal_kado + \App\Models\TransactionDetail::whereDate('created_at', $tglKemarin)->whereHas('transaction', function($q) { $q->where('metode_pembayaran', 'Tunai'); })->where('pemilik', 'Kado Koeni')->sum('subtotal') - $cfH1->hasil_diambil_kado) : 0;
                        $sisaH1Mitra = $cfH1 ? ($cfH1->modal_awal_mitra + \App\Models\TransactionDetail::whereDate('created_at', $tglKemarin)->whereHas('transaction', function($q) { $q->where('metode_pembayaran', 'Tunai'); })->where('pemilik', 'Mitra Anda')->sum('subtotal') - $cfH1->hasil_diambil_mitra) : 0;
                    @endphp
                    <div style="font-size: 11px; color: var(--teks-redup); font-weight: normal; margin-top: 4px;">
                        Data sistem H-1 Kado: Rp {{ number_format($sisaH1Kado, 0, ',', '.') }}<br>
                        Data sistem H-1 Mitra: Rp {{ number_format($sisaH1Mitra, 0, ',', '.') }}
                    </div>
                </td>
                <td>
                    <div style="display:flex; align-items:center;">
                        <span class="teks-redup" style="font-size:12px; margin-right:4px;">Rp</span>
                        <input type="number" name="modal_awal_kado" value="{{ $cashflow->modal_awal_kado }}" class="input-sel">
                    </div>
                </td>
                <td>
                    <div style="display:flex; align-items:center;">
                        <span class="teks-redup" style="font-size:12px; margin-right:4px;">Rp</span>
                        <input type="number" name="modal_awal_mitra" value="{{ $cashflow->modal_awal_mitra }}" class="input-sel">
                    </div>
                </td>
                <td class="angka teks-redup" style="background-color: #F7F5F0;">—</td>
            </tr>
            <tr>
                <td class="keterangan" style="padding-left: 24px;">2. Penjualan Tunai Hari Ini (Uang Masuk Laci)</td>
                <td class="angka">Rp {{ number_format($pemasukanKado, 0, ',', '.') }}</td>
                <td class="angka">Rp {{ number_format($pemasukanMitra, 0, ',', '.') }}</td>
                <td class="angka teks-redup" style="background-color: #F7F5F0;">—</td>
            </tr>
            <tr>
                <td class="keterangan" style="padding-left: 24px;">3. Dikurangi: Bagi Hasil yang Diambil / Pindah Dompet</td>
                <td>
                    <div style="display:flex; align-items:center;">
                        <span style="color: var(--aksen); font-size:12px; margin-right:4px; font-weight:bold;">- Rp</span>
                        <input type="number" name="hasil_diambil_kado" value="{{ $cashflow->hasil_diambil_kado }}" class="input-sel">
                    </div>
                </td>
                <td>
                    <div style="display:flex; align-items:center;">
                        <span style="color: var(--aksen); font-size:12px; margin-right:4px; font-weight:bold;">- Rp</span>
                        <input type="number" name="hasil_diambil_mitra" value="{{ $cashflow->hasil_diambil_mitra }}" class="input-sel">
                    </div>
                </td>
                <td class="angka teks-redup" style="background-color: #F7F5F0;">—</td>
            </tr>
            
            <tr class="baris-total">
                <td class="keterangan" style="padding-left: 24px; font-size: 15px;">SISA KAS LACI MENGINAP UNTUK BESOK (1+2-3)</td>
                <td class="angka">Rp {{ number_format($sisaModalKado ?? 0, 0, ',', '.') }}</td>
                <td class="angka">Rp {{ number_format($sisaModalMitra ?? 0, 0, ',', '.') }}</td>
                <td class="angka" style="background-color: #EAE6DB; font-size: 20px; font-family: 'Fraunces', serif;">
                    Rp {{ number_format($totalSisaModal ?? 0, 0, ',', '.') }}
                </td>
            </tr>

            <!-- BAGIAN B: DOMPET PRIBADI -->
            <tr class="baris-kategori">
                <td colspan="4" style="padding-left: 20px; border-top: 4px solid var(--teks); margin-top: 8px;">
                    B. PERPUTARAN DOMPET PRIBADI (UANG BAGI HASIL)
                </td>
            </tr>
            <tr>
                <td class="keterangan" style="padding-left: 24px;">1. Uang Bagi Hasil Masuk (Dari poin A.3)</td>
                <td class="angka" style="color: #4A6E48;">+ Rp {{ number_format($cashflow->hasil_diambil_kado ?? 0, 0, ',', '.') }}</td>
                <td class="angka" style="color: #4A6E48;">+ Rp {{ number_format($cashflow->hasil_diambil_mitra ?? 0, 0, ',', '.') }}</td>
                <td class="angka teks-redup" rowspan="4" style="background-color: #F7F5F0; text-align: center; vertical-align: middle;">
                    (Area Dompet<br>Tidak Masuk Hitungan Laci)
                </td>
            </tr>
            <tr>
                <td class="keterangan" style="padding-left: 24px;">2. Dikurangi: Beban Pengeluaran Toko</td>
                <td class="angka" style="color: var(--aksen);">- Rp {{ number_format($pengeluaranKado, 0, ',', '.') }}</td>
                <td class="angka" style="color: var(--aksen);">- Rp {{ number_format($pengeluaranMitra, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="keterangan" style="padding-left: 24px;">3. Dikurangi: Penarikan Uang Dadakan / Kasbon</td>
                <td>
                    <div style="display:flex; align-items:center;">
                        <span style="color: var(--aksen); font-size:12px; margin-right:4px; font-weight:bold;">- Rp</span>
                        <input type="number" name="tarik_dadakan_kado" value="{{ $cashflow->tarik_dadakan_kado ?? 0 }}" class="input-sel">
                    </div>
                </td>
                <td>
                    <div style="display:flex; align-items:center;">
                        <span style="color: var(--aksen); font-size:12px; margin-right:4px; font-weight:bold;">- Rp</span>
                        <input type="number" name="tarik_dadakan_mitra" value="{{ $cashflow->tarik_dadakan_mitra ?? 0 }}" class="input-sel">
                    </div>
                </td>
            </tr>
            
            <tr class="baris-total" style="background-color: var(--permukaan);">
                <td class="keterangan" style="padding-left: 24px;">SISA BERSIH DI DOMPET PRIBADI (1-2-3)</td>
                <td class="angka" style="font-size: 16px;">Rp {{ number_format($sisaDompetKado ?? 0, 0, ',', '.') }}</td>
                <td class="angka" style="font-size: 16px;">Rp {{ number_format($sisaDompetMitra ?? 0, 0, ',', '.') }}</td>
            </tr>

        </tbody>
    </table>

    <div style="max-width: 400px; margin: 0 0 0 auto;">
        <button type="submit" class="btn-utama">SIMPAN KERTAS KERJA HARI INI</button>
        <div style="text-align: center; margin-top: 12px; font-size: 12px; color: var(--teks-redup);">
            Data Laci dan Dompet akan otomatis terhitung setelah disimpan.
        </div>
    </div>

</form>

<!-- Form Tersembunyi Penghapusan Beban -->
<form id="form-hapus-exp" method="POST" style="display: none;">
    @csrf @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
    function hapusPengeluaran(id) {
        if (confirm('Hapus catatan beban ini?')) {
            const form = document.getElementById('form-hapus-exp');
            form.action = '/laporan/pengeluaran/' + id;
            form.submit();
        }
    }
</script>
@endsection